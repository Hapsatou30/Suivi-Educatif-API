<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Evaluations;
use App\Http\Requests\StoreEvaluationsRequest;
use App\Http\Requests\UpdateEvaluationsRequest;

class EvaluationsController extends Controller
{
    /**
 * Liste des évaluations du jour 
 */
public function evaluationsJour()
{
    // Récupérer la date du jour
    $dateDuJour = now()->toDateString(); 

   // Récupérer les évaluations pour la date du jour
   $evaluations = Evaluations::with(['classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe','classeProf.anneeClasse.annee'])
   ->whereDate('date', $dateDuJour)
   ->get();

// Filtrer les évaluations pour celles dont l'année est "En_cours"
$evaluationsEnCours = $evaluations->filter(function ($evaluation) {
   return $evaluation->classeProf->anneeClasse->annee->etat === 'En_cours';
});

// Vérifier s'il y a des évaluations pour aujourd'hui et de l'année en cours
if ($evaluationsEnCours->isEmpty()) {
   return response()->json([
       'message' => "Il n'y a pas d'évaluations prévues pour aujourd'hui dans l'année en cours.",
       'status' => 200
   ]);
}

    // Vérifier s'il y a des évaluations pour aujourd'hui
    if ($evaluations->isEmpty()) {
        return response()->json([
            'message' => "Il n'y a pas d'évaluations prévues pour aujourd'hui.",
            'status' => 200
        ]);
    }
    // Transformer les données pour afficher les informations souhaitées
    $resultat = $evaluations->map(function ($evaluation) {
        return [
            'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
            'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom. ' - ' .$evaluation->classeProf->profMatiere->professeur->nom,
            'type' => $evaluation->type_evaluation,
            'date' => $evaluation->date, 
            'classe' => $evaluation->classeProf->anneeClasse->classe->nom,
            'duree' => $evaluation->duree,
            'heure' => $evaluation->heure
        ];
    });

    return response()->json([
        'message' => 'Liste des évaluations du jour',
        'données' => $resultat,
        'status' => 200
    ]);
}

public function evaluationsEleve($eleveId)
{
    // Récupérer les évaluations via les notes pour l'élève
    $notes = Note::with(['evaluation.classeProf.profMatiere.professeur', 'evaluation.classeProf.profMatiere.matiere', 'evaluation.classeProf.anneeClasse.classe'])
                 ->where('eleve_id', $eleveId)
                 ->get();

    // Vérifier s'il y a des évaluations pour cet élève
    if ($notes->isEmpty()) {
        return response()->json([
            'message' => "Il n'y a pas d'évaluations pour cet élève.",
            'status' => 404
        ]);
    }

    // Transformer les données pour afficher les informations souhaitées
    $resultat = $notes->map(function ($note) {
        return [
            'matiere' => $note->evaluation->classeProf->profMatiere->matiere->nom,
            'professeur' => $note->evaluation->classeProf->profMatiere->professeur->prenom . ' ' . $note->evaluation->classeProf->profMatiere->professeur->nom,
            'type_evaluation' => $note->evaluation->type_evaluation,
            'date' => $note->evaluation->date, 
            'classe' => $note->evaluation->classeProf->anneeClasse->classe->nom,
            'duree' => $note->evaluation->duree,
        ];
    });

    // Retourner les données sous forme de JSON
    return response()->json([
        'message' => "Évaluations pour l'élève",
        'evaluations' => $resultat,
        'status' => 200
    ]);
}


               
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
