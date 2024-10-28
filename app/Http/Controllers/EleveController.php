<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Eleve;
use App\Models\Parents;
use App\Models\AnneeClasse;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreEleveRequest;
use App\Http\Requests\UpdateEleveRequest;
use App\Mail\EleveCreated;
use App\Mail\ParentCreated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



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
        $data = $parents->map(function ($parent) {
            return [
                'parent' => [
                    'nom' => $parent->nom,
                    'prenom' => $parent->prenom,
                    'email' => $parent->user->email,
                    'telephone' => $parent->telephone,
                    'adresse' => $parent->adresse
                ],
                'eleves' => $parent->eleves->map(function ($eleve) {
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
    
        // Générer un mot de passe aléatoire de 10 caractères
        $password = Str::random(10);
    
        // Variable pour vérifier si le parent a été créé
        $parentCree = false;
    
        // Si le parent n'existe pas, on le crée
        if (!$parent) {
            // Créer un nouvel utilisateur pour le parent
            $userParent = User::create([
                'email' => $request->parent_email,
                'password' => bcrypt($password), // Mot de passe par défaut
            ]);
    
            // Assigner le rôle "parent" à l'utilisateur
            $userParent->assignRole('parent');
    
            // Créer le parent
            $parent = Parents::create([
                'nom' => $request->parent_nom,
                'prenom' => $request->parent_prenom,
                'telephone' => $request->parent_telephone,
                'adresse' => $request->parent_adresse,
                'photo' => $request->hasFile('parent_photo') ? $request->parent_photo->store('photos') : null,
                'user_id' => $userParent->id,
            ]);
    
            // Marquer le parent comme nouvellement créé
            $parentCree = true;
        }
    
        // Envoyer un email au parent uniquement si le parent a été nouvellement créé
        if ($parentCree) {
            Mail::to($request->parent_email)->send(new ParentCreated($parent, $password));
        }
    
        // Si l'email de l'élève est fourni, l'utiliser. Sinon, générer un email fictif
        $emailEleve = $request->email ?? strtolower($request->nom . '.' . $request->prenom . '@eleve.local');
    
        // Générer un mot de passe aléatoire pour l'élève
        $password = Str::random(10);
    
        // Créer un nouvel utilisateur pour l'élève
        $userEleve = User::create([
            'email' => $emailEleve,
            'password' => bcrypt($password),
        ]);
    
        // Assigner le rôle "élève" à l'utilisateur
        $userEleve->assignRole('eleve');
    
        // Générer une matricule unique pour l'élève
        $prenom = strtoupper(substr($request->prenom, 0, 2));
        $matricule = 'E' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    
        // S'assurer que la matricule est unique pour l'élève
        while (Eleve::where('matricule', $matricule)->exists()) {
            $matricule = 'E' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        }
    
        // Ajouter l'élève à la base de données
        $eleve = Eleve::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'matricule' => $matricule,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : null,
            'date_naissance' => $request->date_naissance,
            'genre' => $request->genre,
            'user_id' => $userEleve->id,
            'parent_id' => $parent->id,
        ]);
    
        // Envoyer un email à l'élève
        Mail::to($emailEleve)->send(new EleveCreated($eleve, $password));
    
        // Retourner la réponse JSON
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

   
public function modifierEleve(Request $request)
{
    // Récupérer l'utilisateur actuellement connecté
    $userAuthId = Auth::id(); // ou Auth::user()->id

    // Récupérer l'élève associé à l'utilisateur connecté
    $eleve = Eleve::where('user_id', $userAuthId)->first();

    if (!$eleve) {
        return response()->json([
            'message' => 'Élève non trouvé pour cet utilisateur',
            'status' => 404
        ]);
    }

    // Récupérer l'utilisateur associé à cet élève
    $user = User::find($userAuthId);

    if (!$user) {
        return response()->json([
            'message' => 'Utilisateur non trouvé',
            'status' => 404
        ]);
    }

    Log::info('Données envoyées pour la mise à jour:', $request->all());

    // Mettre à jour les données de l'élève
    $eleve->nom = $request->input('nom', $eleve->nom);
    $eleve->prenom = $request->input('prenom', $eleve->prenom);
    $eleve->date_naissance = $request->input('date_naissance', $eleve->date_naissance);
    $eleve->genre = $request->input('genre', $eleve->genre);

    // Vérifier si une nouvelle image a été uploadée
    if ($request->hasFile('photo')) {
        // Supprimer l'ancienne photo
        if ($eleve->photo) {
            Storage::disk('public')->delete($eleve->photo);
        }

        // Stocker la nouvelle photo
        $photoPath = $request->file('photo')->store('photos', 'public');
        $eleve->photo = $photoPath; // Stocke le chemin relatif de l'image
    }

    // Mettre à jour les données de l'utilisateur
    if ($request->has('email')) {
        $user->email = $request->input('email');
    }

    if ($request->has('password')) {
        $user->password = bcrypt($request->input('password'));
    }

    // Sauvegarder les modifications
    $eleve->save();
    $user->save();

    return response()->json([
        'message' => 'Élève et utilisateur modifiés avec succès',
        'données' => $eleve,
        'status' => 200
    ]);
}
}
