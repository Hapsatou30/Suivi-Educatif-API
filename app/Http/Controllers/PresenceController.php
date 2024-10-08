<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($classProfId)
    {
        // Récupérer les absences pour la classe avec les informations des élèves, du professeur et de la matière
        $absences = Presence::where('classe_prof_id', $classProfId)
            ->where('status', 'absent')
            ->with([
                'classeEleve.eleve',   
                'classeProf.profMatiere.professeur',
                'classeProf.profMatiere.matiere' 
            ])
            ->get();
    
        // Retourner la liste des absences avec les infos des élèves, du prof et de la matière
        return response()->json([
            'message' => 'Liste des absences pour ce cours',
            'data' => $absences,
            'status' => 200
        ]);
    }
    

   
    public function getAbsences($classeEleveId)
{
    // Récupérer les absences de l'élève spécifié
    $absences = Presence::where('classe_eleve_id', $classeEleveId)
        ->where('status', 'absent')
        ->get();

    // Structurer la réponse en JSON
    return response()->json([
        'message' => 'Liste des absences',
        'données' => $absences,
        'status' => 200
    ]);
}
public function store(StorePresenceRequest $request)
{
    // Récupérer les données validées sans date_presence
    $validated = $request->validated();

    // Ajouter la date_presence automatiquement
    $validated['date_presence'] = now(); // Ajouter la date actuelle

    // Vérifier si le statut est "absent"
    if ($validated['status'] === 'absent') {
        // Créer une nouvelle absence
        $absence = Presence::create($validated);

        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Absence attribuée avec succès.',
            'data' => $absence,
            'status' => 201
        ]);
    }

    // Si le statut n'est pas "absent", retourner une erreur
    return response()->json([
        'message' => 'Le statut doit être "absent" pour enregistrer une absence.',
        'status' => 400
    ]);
}



    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     //voir details d'une absence
    //     $presence = Presence::find($id);
    //     if (!$presence) {
    //         return response()->json([
    //            'message' => 'Absence introuvable',
    //            'status' => 404
    //         ]);
    //     }
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $presence = Presence::findOrFail($id);
    
            $presence->delete();
    
            DB::commit();
    
            return response()->json([
                'message' => 'Absence supprimée avec succès.',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'message' => 'Erreur lors de la suppression de l\'absence.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    
    public function getAbsencesParAnneeClasse($anneeClasseId)
{
    // Récupérer les absences pour l'année de classe spécifiée
    $absences = Presence::whereHas('classeProf', function ($query) use ($anneeClasseId) {
            $query->where('annee_classe_id', $anneeClasseId);
        })
        ->where('status', 'absent')
        ->with([
            'classeEleve.eleve',   
            'classeProf.profMatiere.professeur',
            'classeProf.profMatiere.matiere' 
        ])
        ->get();

    // Retourner la liste des absences avec les infos des élèves, du prof et de la matière
    return response()->json([
        'message' => 'Liste des absences pour l\'année de classe spécifiée',
        'data' => $absences,
        'status' => 200
    ]);
}
    //liste des absences pour les élèves par parent
    public function getAbsencesParParent($parentId)
{
    // Récupérer tous les élèves du parent spécifié
    $eleves = Eleve::where('parent_id', $parentId)->pluck('id');

    // Récupérer les absences des élèves de ce parent
    $absences = Presence::whereHas('classeEleve', function ($query) use ($eleves) {
        $query->whereIn('eleve_id', $eleves);
    })
    ->where('status', 'absent')
    ->with([
        'classeEleve.eleve',   
        'classeProf.profMatiere.professeur',
        'classeProf.profMatiere.matiere' 
    ])
    ->get();

    // Retourner les absences avec les informations des élèves, du professeur et de la matière
    return response()->json([
        'message' => 'Liste des absences pour les enfants du parent spécifié',
        'data' => $absences,
        'status' => 200
    ]);
}

}
