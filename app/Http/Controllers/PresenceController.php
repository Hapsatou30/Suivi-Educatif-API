<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        //
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

public function store(Request $request)
{
    // Récupérer les données, assurez-vous que 'date_presence' est inclus
    $validated = $request->only([
        'date_presence',
        'status',
        'justification',
        'classe_eleve_id',
        'classe_prof_id',
    ]);

    // Vérifier si le statut est "absent"
    if ($validated['status'] === 'absent') {
        // Créer une nouvelle présence
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
    
    
}
