<?php

namespace App\Http\Controllers;

use App\Models\AnneeClasse;
use App\Models\ClasseEleve;
use App\Http\Requests\StoreClasseEleveRequest;
use App\Http\Requests\UpdateClasseEleveRequest;

class ClasseEleveController extends Controller
{
    /**
     * les eleves par annee_classe
     */
    public function index()
{
    // Récupérer les années_classes de l'année en cours avec leurs élèves
    $anneeClasses = AnneeClasse::whereHas('annee', function ($query) {
        $query->where('etat', 'En_cours'); // Filtrer par état "En_cours"
    })
    ->with('classe', 'eleves')
    ->get();

    // Structurer la réponse en JSON
    return response()->json([
        'message' => 'Liste des élèves pour l\'année en cours',
        'données' => $anneeClasses->map(function ($anneeClasse) {
            return [
                'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin, // Afficher l'année scolaire
                'classe' => $anneeClasse->classe->nom, // Nom de la classe
                'eleves' => $anneeClasse->eleves->map(function ($eleve) {
                    return [
                        'nom' => $eleve->nom,
                        'prenom' => $eleve->prenom,
                        'matricule' => $eleve->matricule,
                        'date_naissance' => $eleve->date_naissance,
                        'telephone' => $eleve->telephone,
                    ];
                }),
            ];
        }),
        'status' => 200
    ]);
}


    /**
     * Attribuer des classes aux eleves
     */
    public function store(StoreClasseEleveRequest $request)
    {
        // Récupérer les données validées
        $validated = $request->validated();
    
        // Vérifier si l'année associée est "En_cours"
        $anneeClasse = AnneeClasse::find($validated['annee_classe_id']);
        if ($anneeClasse->annee->etat !== 'En_cours') {
            return response()->json([
                'message' => 'L\'année scolaire doit être en cours pour ajouter un élève.',
                'status' => 400
            ], 400);
        }
    
        // Créer l'entrée dans la table classe_eleves
        $classeEleve = ClasseEleve::create([
            'annee_classe_id' => $validated['annee_classe_id'],
            'eleve_id' => $validated['eleve_id'],
        ]);
    
        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Élève attribué à l\'année classe avec succès',
            'data' => $classeEleve,
            'status' => 201
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(ClasseEleve $classeEleve)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClasseEleve $classeEleve)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClasseEleveRequest $request, ClasseEleve $classeEleve)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClasseEleve $classeEleve)
    {
        //
    }
}
