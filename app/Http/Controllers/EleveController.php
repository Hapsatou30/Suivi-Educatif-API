<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Eleve;
use App\Models\Parents;
use App\Models\AnneeClasse;
use App\Http\Requests\StoreEleveRequest;
use App\Http\Requests\UpdateEleveRequest;

class EleveController extends Controller
{

    //methode pour récuperer le nombre total d'eleve pour l'année en cours 
    public function totalEleves()
    {
        // Récupérer l'année en cours
        $anneeEnCours = AnneeClasse::whereHas('annee', function ($query) {
            $query->where('etat', 'En_cours');
        })->first();
    
        if (!$anneeEnCours) {
            return response()->json([
                'message' => 'Aucune année en cours trouvée.',
                'total' => 0,
                'status' => 404
            ]);
        }
    
        // Compter le nombre total d'élèves associés à l'année en cours
        $totalEleves = $anneeEnCours->eleves()->count();
    
        // Structurer la réponse en JSON
        return response()->json([
            'message' => 'Total d\'élèves pour l\'année en cours.',
            'total' => $totalEleves,
            'status' => 200
        ]);
    }
    
    /**
     *Voir la liste des eleves 
     */
    public function index()
{
    // Récupérer les parents avec leurs élèves
    $parents = Parents::with('eleves')->get();

    // Structurer la réponse JSON pour regrouper les élèves par parent
    $data = $parents->map(function($parent) {
        return [
            'parent' => [
                'nom' => $parent->nom,
                'prenom' => $parent->prenom,
                'email' => $parent->user->email, 
                'telephone' => $parent->telephone,
                'adresse' => $parent->adresse
            ],
            'eleves' => $parent->eleves->map(function($eleve) {
                return [
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                    'matricule' => $eleve->matricule,
                    'date_naissance' => $eleve->date_naissance,
                    'genre' => $eleve->genre,
                    'telephone' => $eleve->telephone,
                ];
            })
        ];
    });

    return response()->json([
        'message' => 'Liste des élèves regroupés par parent',
        'données' => $data,
        'status' => 200
    ]);
}


    /**
     * Methode pour ajouter des eleves
     * 
     */
    public function store(StoreEleveRequest $request)
    {
        // Rechercher un parent existant via son numéro de téléphone
        $parent = Parents::where('telephone', $request->parent_telephone)->first();
        
        // Si le parent n'existe pas, on le crée
        if (!$parent) {
            // Créer un nouvel utilisateur pour le parent
            $userParent = User::create([
                'email' => $request->parent_email,
                'password' => bcrypt('password123'), // Mot de passe par défaut
                'role_id' => 3, // ID du rôle correspondant à un parent
            ]);
    
            // Créer le parent
            $parent = Parents::create([
                'nom' => $request->parent_nom,
                'prenom' => $request->parent_prenom,
                'telephone' => $request->parent_telephone,
                'adresse' => $request->parent_adresse,
                'photo' => $request->hasFile('parent_photo') ? $request->parent_photo->store('photos') : null,
                'user_id' => $userParent->id,
            ]);
        }
    
        // Créer un nouvel utilisateur pour l'élève
        $userEleve = User::create([
            'email' => $request->email,
            'password' => bcrypt('password123'), // Mot de passe fixe pour l'élève
            'role_id' => 4, // ID du rôle correspondant à un élève
        ]);
    
        // Générer une matricule unique pour l'élève
        $prenom = strtoupper(substr($request->prenom, 0, 2)); // Prendre les deux premières lettres du prénom en majuscules
        $matricule = 'E' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    
        // S'assurer que la matricule est unique pour l'élève
        while (Eleve::where('matricule', $matricule)->exists()) {
            $matricule = 'E' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        }
    
        // Ajouter l'élève à la table des élèves
        $eleve = Eleve::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'matricule' => $matricule,
            'telephone' => $request->telephone,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : null, // Sauvegarder l'image dans le dossier public/photos
            'date_naissance' => $request->date_naissance,
            'genre' => $request->genre,
            'user_id' => $userEleve->id, // ID de l'utilisateur créé pour l'élève
            'parent_id' => $parent->id, // ID du parent associé (existant ou créé)
        ]);
    
        // Retourner la réponse JSON avec les données de l'élève créé
        return response()->json([
            'message' => 'Élève créé avec succès',
            'données' => $eleve,
            'status' => 201
        ]);
    }
    
    
    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        // Charger l'élève avec son parent
        $eleve = Eleve::with('parent')->find($id);
   
        // Structurer la réponse avec les détails de l'élève et du parent
        $response = [
            'message' => 'Détails de l\'élève',
            'données' => [
                'eleve' => [
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                    'matricule' => $eleve->matricule,
                    'date_naissance' => $eleve->date_naissance,
                    'genre' => $eleve->genre,
                    'telephone' => $eleve->telephone,
                    'photo' => $eleve->photo,  
                ],
            ],
            'status' => 200
        ];
    
        // Vérifier si le parent existe et l'ajouter à la réponse
        if ($eleve->parent) {
            $response['données']['parent'] = [
                'nom' => $eleve->parent->nom,
                'prenom' => $eleve->parent->prenom,
                'telephone' => $eleve->parent->telephone,
                'adresse' => $eleve->parent->adresse,
                'email' => $eleve->parent->user->email, 
            ];
        } else {
            $response['données']['parent'] = null; 
        }
    
        return response()->json($response);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEleveRequest $request, $id)
    {
        // Récupérer l'élève via son ID
        $eleve = Eleve::findOrFail($id);
    
        // Mettre à jour les informations de l'élève
        // $eleve->update([
        //     'nom' => $request->nom,
        //     'prenom' => $request->prenom,
        //     'telephone' => $request->telephone,
        //     'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : $eleve->photo,  // Sauvegarder l'image dans le dossier public/photos
        //     'date_naissance' => $request->date_naissance,
        //     'genre' => $request->genre,
        //     'parent_id' => $request->parent_id, // ID du parent associé
        // ]);
    
        // // Rafraîchir les données de l'élève pour renvoyer les informations à jour
        // $eleve->refresh();
    
        // // Retourner la réponse JSON avec les données de l'élève modifié
        // return response()->json([
        //     'message' => 'Élève modifié avec succès',
        //     'données' => $eleve,
        //     'status' => 200
        // ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //supprimer un eleve
        $eleve = Eleve::findOrFail($id);
        $eleve->delete();

        return response()->json([
           'message' => 'Élève supprimé avec succès',
           'status' => 200
        ]);
    }
}
