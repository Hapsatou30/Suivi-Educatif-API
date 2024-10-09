<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    //Methode pour gerer la connexion

        public function login(Request $request)
        {
            // Validation des données
            $validator = validator(
                $request->all(),
                [
                    'email' => 'required|email|string',
                    'password' => 'required|string|min:8',
                ]
            );
            // Si les données ne sont pas valides, renvoyer les erreurs
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
            // Si les données sont valides, authentifier l'utilisateur
            $credentials = $request->only('email', 'password');
            $token = auth()->attempt($credentials);

                    // Récupérer l'utilisateur authentifié
            $user = auth()->user();

            // Récupérer les rôles de l'utilisateur
            $roles = $user->getRoleNames(); // Méthode Spatie pour obtenir les noms de rôles

            // Si les informations de connexion ne sont pas correctes, renvoyer une erreur 401  
            if (!$token) {
                return response()->json(['message' => 'Information de connexion incorrectes'], 401);
            }
            // Renvoyer le token d'authentification
            return response()->json([
                "access_token" => $token,
                "token_type" => "bearer",
                "user" => auth()->user(),
                "roles" => $roles,
                "expires_in" => env("JwT_TTL") * 60  . 'seconds'
            ]);
        }

    //methode pour la déconnexion

        public function logout()
        {
            // Supprimer le jeton d'authentification
            auth()->logout();
            // Renvoyer une réponse avec un message de succès
            return response()->json(['message' => 'Déconnexion réussie']);
        }

        //methode pour rafraichir
        public function refresh()
        {
            // Renouveler le jeton d'authentification
            $token = auth()->refresh();
            // Renvoyer le nouveau jeton d'authentification
            return response()->json([
                "access_token" => $token,
                "token_type" => "bearer",
                "user" => auth()->user(),
                "expires_in" => env("JWT_TTL") * 240  .'seconds'
            ]);
        }
    
    //methode pour récuperer les informations du user
    public function profile() {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();
    
        // Récupérer les rôles de l'utilisateur
        $roles = $user->getRoleNames(); // Méthode Spatie pour obtenir les noms de rôles
    
        // Variable pour stocker le prénom
        $prenom = null;
    
        // Vérification des rôles et récupération du prénom dans la table correspondante
        if ($roles->contains('professeur')) {
            // Si l'utilisateur est un professeur
            $professeur = $user->professeur; // Relation avec la table 'professeurs'
            $prenom = $professeur ? $professeur->prenom : null;
        } elseif ($roles->contains('parent')) {
            // Si l'utilisateur est un parent
            $parent = $user->parent; // Relation avec la table 'parents'
            $prenom = $parent ? $parent->prenom : null;
        } elseif ($roles->contains('eleve')) {
            // Si l'utilisateur est un élève
            $eleve = $user->eleve; // Relation avec la table 'eleves'
            $prenom = $eleve ? $eleve->prenom : null;
        }
    
        // Renvoyer les informations du user avec ses rôles et le prénom
        return response()->json([
            "user" => $user,
            "roles" => $roles,
            "prenom" => $prenom
        ]);
    }
    
}
