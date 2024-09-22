<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Liste des notes par matiere
     */
    public function index($classProfId)
    {
        // Récupérer les notes pour une matière spécifique avec les évaluations et élèves associés
        $notes = Note::with(['evaluation.classeProf.profMatiere.matiere', 'eleve'])
            ->whereHas('evaluation.classeProf', function($query) use ($classProfId) {
                $query->where('id', $classProfId);
            })
            ->get();
    
        // Vérifier si des notes existent
        if ($notes->isEmpty()) {
            return response()->json([
                'message' => 'Aucune note trouvée pour cette matière.',
                'status' => 404
            ]);
        }
    
        // Préparer la réponse avec la liste des notes pour la matière spécifiée
        $result = [];
        foreach ($notes as $note) {
            $matiere = $note->evaluation->classeProf->profMatiere->matiere;
            $eleve = $note->eleve;
    
            $result[] = [
                'matiere' => $matiere->nom,
                'note' => $note->notes, 
                'evaluation' => $note->evaluation->type_evaluation,
                'eleve' => [
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                ],
            ];
        }
    
        return response()->json([
            'message' => 'Liste des notes pour la matière spécifiée',
            'données' => $result,
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
