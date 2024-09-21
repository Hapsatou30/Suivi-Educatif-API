<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        // Créer une nouvelle note
        $note = Note::create([
            'notes' => $request->input('notes'),
            'commentaire' => $request->input('commentaire'),
            'evaluation_id' => $request->input('evaluation_id'),
            'eleve_id' => $request->input('eleve_id'),
            'bulletin_id' => $request->input('bulletin_id'),
        ]);
    
        // Structurer la réponse en JSON
        return response()->json([
            'message' => 'Note ajoutée avec succès.',
            'données' => $note,
            'status' => 201
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        //
    }
}
