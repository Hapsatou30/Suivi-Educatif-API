<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parents;
use App\Http\Requests\StoreParentsRequest;
use App\Http\Requests\UpdateParentsRequest;

class ParentsController extends Controller
{
    /**
     * Liste des parents
     */
    public function index()
    {
        //liste des parents
        $parents = Parents::all();
        return response()->json ([
           'message' => 'Liste des parents',
           'données' => $parents,
           'status' => 200
        ]);
    }

    /**
     * 
     */
    public function store(StoreParentsRequest $request)
    {
        // Définir un mot de passe par défaut pour l'utilisateur
        $password = 'password123';  // Mot de passe fixe ou à générer dynamiquement
    
        // Créer un nouvel utilisateur pour le parent
        $user = User::create([
            'email' => $request->email,  // Assure-toi que l'email est bien unique
            'password' => bcrypt($password),
            'role_id' => 3,  // ID du rôle correspondant à un parent (vérifie bien le rôle associé)
        ]);
    
        // Ajouter le parent à la table des parents
        $parent = Parents::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : null,  // Sauvegarder l'image dans le dossier public/photos si fournie
            'user_id' => $user->id,  // ID de l'utilisateur créé
        ]);
    
        // Retourner la réponse JSON avec les données du parent créé
        return response()->json([
            'message' => 'Parent créé avec succès',
            'données' => $parent,
            'status' => 201
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Parents $parents)
    {
        //
    }

  

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParentsRequest $request, $id)
    {
        // Récupérer le parent via son ID
        $parent = Parents::findOrFail($id); // ParentModel est à remplacer par le nom correct de votre modèle Parent, par exemple 'Parents'
    
        // Mettre à jour les informations du parent
        $parent->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : $parent->photo,  // Sauvegarder l'image dans le dossier public/photos
        ]);
    
        // Rafraîchir les données du parent pour renvoyer les informations à jour
        $parent->refresh();
    
        // Retourner la réponse JSON avec les données du parent modifié
        return response()->json([
            'message' => 'Parent modifié avec succès',
            'données' => $parent,
            'status' => 200
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //supprimer un parent
        $parent = Parents::find($id);
        $parent->delete();

        return response()->json([
           'message' => 'Parent supprimé avec succès',
           'status' => 200
        ]);
    }
}
