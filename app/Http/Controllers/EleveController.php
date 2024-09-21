<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Eleve;
use App\Http\Requests\StoreEleveRequest;
use App\Http\Requests\UpdateEleveRequest;

class EleveController extends Controller
{
    /**
     *Voir la liste des eleves 
     */
    public function index()
    {
        //liste des eleves 
        $eleves = Eleve::all();
        return response()->json ([
           'message' => 'Liste des élèves',
           'données' => $eleves,
           'status' => 200
        ]);

    }

    

    /**
     * Methode pour ajouter des eleves
     * 
     */
    public function store(StoreEleveRequest $request)
    {
        // Définir un mot de passe fixe pour l'élève
        $password = 'password123'; // Mot de passe fixe pour l'élève
    
        // Créer un nouvel utilisateur pour l'élève
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($password),
            'role_id' => 4, // ID du rôle correspondant à un élève
        ]);
    
        // Générer une matricule unique pour l'élève
        $prenom = strtoupper(substr($request->prenom, 0, 2)); // Prendre les deux premières lettres du prénom en majuscules
        $matricule = 'E' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Générer trois chiffres aléatoires
    
        // S'assurer que la matricule est unique pour l'élève
        while (Eleve::where('matricule', $matricule)->exists()) {
            // Régénérer une nouvelle matricule si elle existe déjà
            $matricule = 'E' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        }
    
        // Ajouter l'élève à la table des élèves
        $eleve = Eleve::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'matricule' => $matricule,
            'telephone' => $request->telephone,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : null,  // Sauvegarder l'image dans le dossier public/photos
            'date_naissance' => $request->date_naissance,
            'genre' => $request->genre,
            'user_id' => $user->id, // ID de l'utilisateur créé
            'parent_id' => $request->parent_id, // ID du parent associé
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
    public function show(Eleve $eleve)
    {
        //
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEleveRequest $request, $id)
    {
        // Récupérer l'élève via son ID
        $eleve = Eleve::findOrFail($id);
    
        // Mettre à jour les informations de l'élève
        $eleve->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : $eleve->photo,  // Sauvegarder l'image dans le dossier public/photos
            'date_naissance' => $request->date_naissance,
            'genre' => $request->genre,
            'parent_id' => $request->parent_id, // ID du parent associé
        ]);
    
        // Rafraîchir les données de l'élève pour renvoyer les informations à jour
        $eleve->refresh();
    
        // Retourner la réponse JSON avec les données de l'élève modifié
        return response()->json([
            'message' => 'Élève modifié avec succès',
            'données' => $eleve,
            'status' => 200
        ]);
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
