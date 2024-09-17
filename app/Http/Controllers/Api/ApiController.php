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
            // Si les informations de connexion ne sont pas correctes, renvoyer une erreur 401  
            if (!$token) {
                return response()->json(['message' => 'Information de connexion incorrectes'], 401);
            }
            // Renvoyer le token d'authentification
            return response()->json([
                "access_token" => $token,
                "token_type" => "bearer",
                "user" => auth()->user(),
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
                "expires_in" => env("JWT_TTL") * 60  .'seconds'
            ]);
        }
    
    
}
