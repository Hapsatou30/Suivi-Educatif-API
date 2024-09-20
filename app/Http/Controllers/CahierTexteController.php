<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCahierTexteRequest;
use App\Http\Requests\UpdateCahierTexteRequest;
use App\Models\CahierTexte;

class CahierTexteController extends Controller
{
    /**
     * Methode pour afficher les contenu du cahier de texte
     */
    public function index()
    {
        // Récupérer les cahiers de texte avec les relations
        $cahiersTexte = CahierTexte::with([
            'classeProf.profMatiere.professeur',    // Récupérer les informations du professeur
            'classeProf.profMatiere.matiere', // Récupérer les informations de la matière
            'classeProf.anneeClasse.classe',  // Récupérer les informations de la classe
            'classeProf.anneeClasse.annee'    // Récupérer les informations de l'année
        ])->get();
    
        // Transformer les données pour les rendre plus lisibles
        $result = $cahiersTexte->map(function ($cahier) {
            return [
                'titre' => $cahier->titre,
                'resume' => $cahier->resume,
                'date' => $cahier->date,
                'professeur' => $cahier->classeProf->profMatiere->professeur->prenom,
                'matiere' => $cahier->classeProf->profMatiere->matiere->nom,
                'classe' => $cahier->classeProf->anneeClasse->classe->nom,
                'annee' => $cahier->classeProf->anneeClasse->annee->annee_debut . ' - ' . $cahier->classeProf->anneeClasse->annee->annee_fin
            ];
        });
    
        return response()->json([
            'message' => 'Liste des cahiers de texte',
            'données' => $result,
            'status' => 200
        ]);
    }
    
   

    /**
     * Methode pour ajouter un cahier de texte
     */
    public function store(StoreCahierTexteRequest $request)
    {
        //ajouter un cahier de texte
        $cahierTexte = CahierTexte::create($request->all());
        return response()->json([
           'message' => 'Cahier de texte créé avec succès',
           'données' => $cahierTexte,
           'status' => 201
        ]);
    }

    /**
     * Metode pour voir le cahier de texte pour une classe
     */
    public function show($classeId)
{
    // Récupérer les cahiers de texte associés à cette classe via la relation classeProf
    $cahiersTexte = CahierTexte::whereHas('classeProf', function ($query) use ($classeId) {
        $query->whereHas('anneeClasse', function ($q) use ($classeId) {
            $q->where('classe_id', $classeId);
        });
    })
    ->with([
        'classeProf.profMatiere.professeur',    // Récupérer les informations du professeur
        'classeProf.profMatiere.matiere', // Récupérer les informations de la matière
        'classeProf.anneeClasse.classe',  // Récupérer les informations de la classe
        'classeProf.anneeClasse.annee'    // Récupérer les informations de l'année
    ])
    ->get();

    // Vérifier si des cahiers de texte existent pour cette classe
    if ($cahiersTexte->isEmpty()) {
        return response()->json([
            'message' => 'Aucun cahier de texte trouvé pour cette classe.',
            'status' => 404
        ]);
    }

    // Transformer les données pour les rendre plus lisibles
    $result = $cahiersTexte->map(function ($cahier) {
        return [
            'titre' => $cahier->titre,
            'resume' => $cahier->resume,
            'date' => $cahier->date,
            'professeur' => $cahier->classeProf->profMatiere->professeur->prenom,
            'matiere' => $cahier->classeProf->profMatiere->matiere->nom,
            'classe' => $cahier->classeProf->anneeClasse->classe->nom,
            'annee' => $cahier->classeProf->anneeClasse->annee->annee_debut . ' - ' . $cahier->classeProf->anneeClasse->annee->annee_fin
        ];
    });

    return response()->json([
        'message' => 'Cahiers de texte pour la classe spécifiée',
        'données' => $result,
        'status' => 200
    ]);
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CahierTexte $cahierTexte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCahierTexteRequest $request, CahierTexte $cahierTexte)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CahierTexte $cahierTexte)
    {
        //
    }
}
