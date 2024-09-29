<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnneeScolaireRequest;
use App\Http\Requests\UpdateAnneeScolaireRequest;
use App\Models\AnneeScolaire;

class AnneeScolaireController extends Controller
{
    /**
     * méthode pour afficher la liste des annee scolaire
     */
    public function index()
    {
       //afficher la liste des annee scolaire
       $annees = AnneeScolaire::all();
       return response()->json([
           'message' => 'Liste des années scolaires',
           'données' => $annees,
           'status' => 200
       ]);
    }

   

    /**
     * Méthode pour ajouter une année scolaire
     */
    public function store(StoreAnneeScolaireRequest $request)
    {
        // Vérifier si une année scolaire en cours existe déjà
        $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();
        if ($anneeScolaireEnCours) {
            return response()->json([
                'message' => 'Il existe déjà une année scolaire en cours.',
                'status' => 409
            ]);
        }
    
        // Vérifier si une année scolaire avec les mêmes dates existe déjà
        $anneeExistante = AnneeScolaire::where('annee_debut', $request->annee_debut)
        ->where('annee_fin', $request->annee_fin)
        ->first();

        if ($anneeExistante) {
        return response()->json([
        'message' => 'Une année scolaire avec les mêmes dates existe déjà.',
        'status' => 409
        ]);
        }
    
        // Créer une nouvelle année scolaire
        $annee = AnneeScolaire::create($request->all());
        return response()->json([
            'message' => 'Année scolaire créée avec succès',
            'données' => $annee,
            'status' => 201
        ]);
    }
    

    /**
     *Methode pour voir les details d'une annéee scolaire créée
     */
    public function show($id)
    {
        $anneeScolaire = AnneeScolaire::find($id); 
    
        if (!$anneeScolaire) {
            return response()->json([
                'message' => "L'année scolaire avec ID $id n'a pas été trouvée.",
                'status' => 404,
            ], 404);
        }
    
        return response()->json([
            'message' => 'Détails de l\'année scolaire',
            'données' => $anneeScolaire,
            'status' => 200
        ]);
    }
    

 

    /**
     * Methode pour modifier une année scolaire
     */
    public function update(UpdateAnneeScolaireRequest $request, $id)
    {
        // Chercher l'année scolaire par ID ou renvoyer une erreur 404
        $anneeScolaire = AnneeScolaire::find($id);
    
        if (!$anneeScolaire) {
            return response()->json([
                'message' => "L'année scolaire avec ID $id n'a pas été trouvée.",
                'status' => 404
            ], 404);
        }
    
       

        // Mise à jour des informations de l'année scolaire
        $anneeScolaire->update($request->all());
    
        return response()->json([
            'message' => 'Année scolaire modifiée avec succès',
            'données' => $anneeScolaire,
            'status' => 200
        ]);
    }
    

    /**
     * methode pour supprimer une année
     */
    public function destroy($id)
    {
        // Récupérer l'année scolaire, y compris les enregistrements supprimés (soft deleted)
        $anneeScolaire = AnneeScolaire::withTrashed()->find($id);
    
        // Vérifier si l'année scolaire existe, sinon retourner une réponse 404
        if (!$anneeScolaire) {
            return response()->json([
                'message' => "L'année scolaire avec ID $id n'existe pas.",
                'status' => 404
            ], 404);
        }
    
        // Vérifier si l'année scolaire est déjà supprimée via soft delete
        if ($anneeScolaire->trashed()) {
            return response()->json([
                'message' => "L'année scolaire avec ID $id a déjà été supprimée.",
                'status' => 400
            ], 400);
        }
    
        // Supprimer l'année scolaire (utilise soft delete)
        $anneeScolaire->delete();
    
        // Retourner une réponse JSON indiquant que la suppression a été effectuée avec succès
        return response()->json([
            'message' => 'Année scolaire supprimée avec succès',
            'status' => 200
        ]);
    }
    
}
