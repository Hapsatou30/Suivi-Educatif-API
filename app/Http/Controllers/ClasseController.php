<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClasseRequest;
use App\Http\Requests\UpdateClasseRequest;
use App\Models\Classe;

class ClasseController extends Controller
{
    /**
     * Methode pour afficher la liste des classes
     */
    public function index()
    {
        //liste des classe
        $classes = Classe::all();
        return response()->json([
           'message' => 'Liste des classes',
           'données' => $classes,
           'status' => 200
        ]);
    }

   

    /**
     * Methode pour ajouter une classe
     */
    public function store(StoreClasseRequest $request)
    {
        //ajouter une classe
        $classe = Classe::create($request->all());
        return response()->json([
           'message' => 'Classe créée avec succès',
           'données' => $classe,
           'status' => 201
        ]);
    }

    /**Detail d'une classe
     */
    public function show($id)
    {
        //verification de la classe
        $classe = Classe::find($id);
        if (!$classe) {
            return response()->json([
               'message' => 'Classe introuvable',
               'status' => 404
            ]);
        }
        return response()->json([
           'message' => 'Détail de la classe',
           'données' => $classe,
           'status' => 200
        ]);
    }

    /**
     * Methode pour modification
     */
    public function update(UpdateClasseRequest $request, $id)
    {
        //verification de la classe
        $classe = Classe::find($id);
        if (!$classe) {
            return response()->json([
               'message' => 'Classe introuvable',
               'status' => 404
            ]);
        }
        // modifier les informations de la classe
        $classe->update($request->all());
        return response()->json([
           'message' => 'Classe modifiée avec succès',
           'données' => $classe,
           'status' => 200
        ]);
    }

    /**
     * Methode pour supprimer une classe
     */
    public function destroy($id)
    {
        // Récupérer classe, y compris les enregistrements supprimés (soft deleted)
        $classe = Classe::withTrashed()->find($id);
    
        // Vérifier si classe existe, sinon retourner une réponse 404
        if (!$classe) {
            return response()->json([
                'message' => "classe avec ID $id n'existe pas.",
                'status' => 404
            ], 404);
        }
    
        // Vérifier si classe est déjà supprimée via soft delete
        if ($classe->trashed()) {
            return response()->json([
                'message' => "classe avec ID $id a déjà été supprimée.",
                'status' => 400
            ], 400);
        }
    
        // Supprimer classe (utilise soft delete)
        $classe->delete();
    
        // Retourner une réponse JSON indiquant que la suppression a été effectuée avec succès
        return response()->json([
            'message' => 'Classe supprimée avec succès',
            'status' => 200
        ]);
    }
}
