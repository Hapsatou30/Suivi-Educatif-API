<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatiereRequest;
use App\Http\Requests\UpdateMatiereRequest;
use App\Models\Matiere;

class MatiereController extends Controller
{
    /**
     *methode pour afficher la liste des matieres
     */
    public function index()
    {
        $matieres = Matiere::all();
        return response()->json ([
           'message' => 'Liste des matieres',
           'données' => $matieres,
           'status' => 200
        ]);
    }


     //methode pour créer une matiere
    public function store(StoreMatiereRequest $request)
    {
        //creer une matiere
        $matiere = Matiere::create($request->all());
        return response()->json ([
           'message' => 'Matiere créée avec succès',
           'données' => $matiere,
           'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Matiere $matiere)
    {
        //voir les details pour une matiere
        return response()->json ([
           'message' => 'Détails de la matière',
           'données' => $matiere,
           'status' => 200
        ]);
    }

   
    /**
     * methode pour modifier les informations d'une matiere
     */
    public function update(UpdateMatiereRequest $request, Matiere $matiere)
    {
        //modifier les informations d'une matiere
        $matiere->update($request->all());
        return response()->json ([
           'message' => 'Matiere modifiée avec succès',
           'données' => $matiere,
           'status' => 200
        ]);
    }

    /**
     * methode pour supprimer une matiere
     */
    public function destroy(Matiere $matiere)
    {
        //supprimer une matiere
        $matiere->delete();
        return response()->json ([
           'message' => 'Matiere supprimée avec succès',
           'status' => 200
        ]);
    }
}
