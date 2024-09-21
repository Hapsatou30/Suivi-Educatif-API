<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use App\Models\Presence;

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

    public function store(StorePresenceRequest $request)
{
    // Récupérer les données validées
    $validated = $request->validated();

    // Créer une nouvelle présence
    $presence = Presence::create([
        'date_presence' => $validated['date_presence'],
        'status' => $validated['status'],
        'motif' => $validated['motif'],
        'justification' => $validated['justification'],
        'classe_eleve_id' => $validated['classe_eleve_id'],
        'classe_prof_id' => $validated['classe_prof_id'],
    ]);

    // Retourner une réponse JSON
    return response()->json([
        'message' => 'Présence attribuée avec succès.',
        'data' => $presence,
        'status' => 201
    ]);
}


    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        //
    }

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
    public function destroy(Presence $presence)
    {
        //
    }
}
