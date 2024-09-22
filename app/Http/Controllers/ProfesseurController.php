<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Professeur;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfesseurRequest;
use App\Http\Requests\UpdateProfesseurRequest;

class ProfesseurController extends Controller
{

    //nombre total de professeur
    public function totalProfesseurs()
    {

        // Compter le nombre total de prof 
        $totalProsseur = Professeur::count();
    
        // Structurer la réponse en JSON
        return response()->json([
            'message' => 'Total d\'élèves pour l\'année en cours.',
            'total' => $totalProsseur,
            'status' => 200
        ]);
    }
    /**
     * Afficher la liste des professeurs
     */
    public function index()
{
    // Récupérer tous les professeurs avec leurs emails (s'ils sont dans une relation avec 'users' par exemple)
    $professeurs = Professeur::with('user')->get(); // Suppose que la relation 'user' existe dans le modèle Professeur

    // Transformer les données pour inclure les emails
    $resultat = $professeurs->map(function ($professeur) {
        return [
            'id' => $professeur->id,
            'nom' => $professeur->nom,
            'prenom' => $professeur->prenom,
            'telephone' => $professeur->telephone,
            'matricule' => $professeur->matricule,
            'email' => $professeur->user->email, 
        ];
    });

    return response()->json([
        'message' => 'Liste des professeurs',
        'données' => $resultat,
        'status' => 200
    ]);
}


   

    /**
     * Methode pour ajouter un professeur
     */
    public function store(StoreProfesseurRequest $request)
    {
    
        // Définir un mot de passe fixe
        $password = 'password123'; // Mot de passe fixe

    
        // Créer un nouvel utilisateur
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($password),
            'role_id' => 2, // ID du rôle correspondant à un professeur
        ]);
    
        // Générer une matricule unique
        $prenom = strtoupper(substr($request->prenom, 0, 2)); // Prendre les deux premières lettres du prénom en majuscules
        $matricule = 'P' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Générer trois chiffres aléatoires
    
        // S'assurer que la matricule est unique
        while (Professeur::where('matricule', $matricule)->exists()) {
            // Régénérer une nouvelle matricule si elle existe déjà
            $matricule = 'P' . $prenom . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        }
    
        // Ajouter le professeur à la table des professeurs
        $professeur = Professeur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'matricule' => $matricule,
            'telephone' => $request->telephone,
            'photo' => $request->hasFile('photo') ? $request->photo->store('photos') : null,  // Sauvegarder l'image dans le dossier public/photos
            'user_id' => $user->id, // ID de l'utilisateur créé
        ]);
    
        return response()->json([
            'message' => 'Professeur créé avec succès',
            'données' => $professeur,
            'status' => 201
        ]);
    }
    

    /**
     *voir les details d'un professeur
     */
    public function show(Professeur $professeur)
    {
        return response()->json([
           'message' => 'Détails du professeur',
           'données' => $professeur,
           'status' => 200
        ]);
    }

    
    /**
     * ;ethode pour supprimer un professeur
     */
    public function update(UpdateProfesseurRequest $request, Professeur $professeur)
    {
        // Mettre à jour les informations du professeur
        $professeur->update($request->all());
        return response()->json([
           'message' => 'Professeur modifié avec succès',
           'données' => $professeur,
           'status' => 200
        ]);
    }

    /**
     * Supprimer un professeur
     */
    public function destroy(Professeur $professeur)
    {
        // Supprimer le professeur
        $professeur->delete();
        return response()->json([
           'message' => 'Professeur supprimé avec succès',
           'status' => 200
        ]);
    }
}
