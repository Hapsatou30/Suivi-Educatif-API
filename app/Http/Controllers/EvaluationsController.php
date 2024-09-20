<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationsRequest;
use App\Http\Requests\UpdateEvaluationsRequest;
use App\Models\Evaluations;

class EvaluationsController extends Controller
{
    /**
 * Liste des évaluations par classe
 */
   /**
 * Liste des évaluations par classe
 */
    public function index()
    {
        // Récupérer toutes les évaluations avec les relations nécessaires
        $evaluations = Evaluations::with(['classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe'])->get();

        // Transformer les données pour afficher les informations souhaitées
        $resultat = $evaluations->map(function ($evaluation) {
            return [
                'matiere' => $evaluation->classeProf->profMatiere->matiere->nom, // Nom de la matière
                'nom' => $evaluation->nom,
                'date' => $evaluation->date, // Date de l'évaluation
                'heure' => $evaluation->heure, // Heure de l'évaluation
                'duree' => $evaluation->duree, // Durée de l'évaluation
                'classe' => $evaluation->classeProf->anneeClasse->classe->nom, // Nom de la classe
                'prenom_professeur' => $evaluation->classeProf->profMatiere->professeur->prenom, // Prénom du professeur
            ];
        });

        return response()->json([
            'message' => 'Liste des évaluations',
            'données' => $resultat,
            'status' => 200
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Ajouter une évaluation
     */
    public function store(StoreEvaluationsRequest $request)
    {
        // Récupérer les données validées
        $data = $request->validated();
    
        // Vérifier si une évaluation existe déjà pour cette classe à la même date et heure
        $evaluationExistante = Evaluations::where('classe_prof_id', $data['classe_prof_id'])
            ->where('date', $data['date'])
            ->where('heure', $data['heure'])
            ->first();
    
        if ($evaluationExistante) {
            return response()->json([
                'message' => 'Une évaluation existe déjà pour cette classe à cette date et heure.',
                'status' => 400
            ]);
        }
    
        // Ajouter l'évaluation
        $evaluation = Evaluations::create($data);
    
        return response()->json([
            'message' => 'Évaluation créée avec succès',
            'données' => $evaluation,
            'status' => 201
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Evaluations $evaluations)
    {
        //
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEvaluationsRequest $request, $id)
{
    // Récupérer les données validées
    $data = $request->validated();

    // Récupérer l'évaluation existante par son ID
    $evaluation = Evaluations::findOrFail($id);

    // Vérifier si une autre évaluation existe déjà pour cette classe à la même date et heure
    $evaluationExistante = Evaluations::where('classe_prof_id', $data['classe_prof_id'])
        ->where('date', $data['date'])
        ->where('heure', $data['heure'])
        ->where('id', '!=', $evaluation->id) // Ignorer l'évaluation actuelle
        ->first();

    if ($evaluationExistante) {
        return response()->json([
            'message' => 'Une évaluation existe déjà pour cette classe à cette date et heure.',
            'status' => 400
        ]);
    }

    // Mettre à jour l'évaluation
    $evaluation->update($data);

    return response()->json([
        'message' => 'Évaluation mise à jour avec succès',
        'données' => $evaluation,
        'status' => 200
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //supprimer une evaluation
        $evaluation = Evaluations::findOrFail($id);
        $evaluation->delete();

        return response()->json([
           'message' => 'Évaluation supprimée avec succès',
           'status' => 200
        ]);
    }
}
