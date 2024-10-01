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
//     public function totalEleves()
// {
//     // Compter le nombre total d'élèves dans la base de données
//     $totalEleves = Eleve::count();

//     // Structurer la réponse en JSON
//     return response()->json([
//         'message' => 'Total d\'élèves dans la base de données.',
//         'total' => $totalEleves,
//         'status' => 200
//     ]);
// }

    
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
    /**
 * Display the specified resource.
 */
public function show($id)
{
    // Charger l'élève avec son parent
    $eleve = Eleve::with('parent')->find($id);
    
    // Vérifier si l'élève existe
    if (!$eleve) {
        return response()->json([
            'message' => 'Élève non trouvé',
            'status' => 404
        ]);
    }

    // Structurer la réponse avec les détails de l'élève et du parent
    $response = [
        'message' => 'Détails de l\'élève',
        'données' => [
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'matricule' => $eleve->matricule,
                'date_naissance' => $eleve->date_naissance,
                'genre' => $eleve->genre,
                'telephone' => $eleve->telephone,
                'photo' => $eleve->photo,  
            ],
            'parent' => [
                'nom_parent' => $eleve->parent->nom,
                'prenom_parent' => $eleve->parent->prenom,
                'telephone_parent' => $eleve->parent->telephone,
                'adresse_parent' => $eleve->parent->adresse,
                'email_parent' => $eleve->parent->user->email, 
            ]
        ],
        'status' => 200
    ];

    return response()->json($response);
}

/**
 * Récupérer la liste de tous les élèves avec leurs parents.
 */
public function eleves()
{
    // Charger tous les élèves avec leurs parents
    $eleves = Eleve::with('parent')->get();

    // Structurer la réponse avec les détails de chaque élève et de son parent
    $response = [
        'message' => 'Liste des élèves',
        'données' => $eleves->map(function ($eleve) {
            return [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'matricule' => $eleve->matricule,
                'date_naissance' => $eleve->date_naissance,
                'genre' => $eleve->genre,
                'telephone' => $eleve->telephone,
                'photo' => $eleve->photo,
                'email' => $eleve->user->email,
                'parent' => [
                    'nom_parent' => $eleve->parent->nom,
                    'prenom_parent' => $eleve->parent->prenom,
                    'telephone_parent' => $eleve->parent->telephone,
                    'adresse_parent' => $eleve->parent->adresse,
                    'email_parent' => $eleve->parent->user->email,
                ]
            ];
        }),
        'status' => 200
    ];

    return response()->json($response);
}
 

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEleveRequest $request, $id)
{
    // Rechercher l'élève par son ID
    $eleve = Eleve::findOrFail($id);

    // Mettre à jour les informations de l'élève
    $eleve->update([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'telephone' => $request->telephone,
        'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : $eleve->photo, // Conserver l'ancienne photo si aucune nouvelle n'est fournie
        'date_naissance' => $request->date_naissance,
        'genre' => $request->genre,
    ]);

    // Mettre à jour les informations du parent
    $parent = Parents::findOrFail($eleve->parent_id); // On récupère le parent associé à l'élève
    $parent->update([
        'nom' => $request->parent_nom,
        'prenom' => $request->parent_prenom,
        'adresse' => $request->parent_adresse,
        'telephone' => $request->parent_telephone,
        'photo' => $request->hasFile('parent_photo') ? $request->parent_photo->store('photos') : $parent->photo, // Conserver l'ancienne photo si aucune nouvelle n'est fournie
    ]);

    // Retourner la réponse JSON avec les données de l'élève et du parent mis à jour
    return response()->json([
        'message' => 'Élève et parent mis à jour avec succès',
        'données' => [
            'eleve' => $eleve,
            'parent' => $parent
        ],
        'status' => 200
    ]);
}

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Récupérer l'élève
        $eleve = Eleve::findOrFail($id);
    
        // Supprimer l'utilisateur associé
        if ($eleve->user_id) {
            $user = User::find($eleve->user_id);
            if ($user) {
                $user->delete();
            }
        }
    
        // Supprimer l'élève
        $eleve->delete();
    
        return response()->json([
            'message' => 'Élève et utilisateur supprimés avec succès',
            'status' => 200
        ]);
    }
    
}
