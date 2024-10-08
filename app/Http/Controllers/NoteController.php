<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Eleve;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\ClasseEleve;

class NoteController extends Controller
{
    /**
     * Liste des notes par matiere
     */
    public function index($classe_prof_id)
    {
        // Récupérer les notes pour la classe du professeur associée à un ID de classe_prof
        $notes = Note::with([
            'evaluation.classeProf.profMatiere.matiere',
            'evaluation.classeProf.profMatiere.professeur', 
            'classeEleve.eleve'
        ])
        ->whereHas('evaluation', function($query) use ($classe_prof_id) {
            $query->where('classe_prof_id', $classe_prof_id);
        })
        ->get();
    
        // Vérifier si des notes existent
        if ($notes->isEmpty()) {
            return response()->json([
                'message' => 'Aucune note trouvée pour cette classe.',
                'status' => 404
            ]);
        }
    
        // Préparer la réponse avec la liste des notes pour la classe du professeur
        $result = [];
        foreach ($notes as $note) {
            // Récupérer les informations sur la matière et le professeur
            $matiere = $note->evaluation->classeProf->profMatiere->matiere;
            $professeur = $note->evaluation->classeProf->profMatiere->professeur; // Assurez-vous que cette relation est définie
    
            $eleve = $note->classeEleve->eleve; // On obtient l'élève associé à la note
            $classeEleve = $note->classeEleve; // On obtient la classe de l'élève
    
            $result[] = [
                'id' => $note->id,
                'matiere' => $matiere->nom, // Nom de la matière
                'professeur' => [
                    'nom' => $professeur->nom,
                    'prenom' => $professeur->prenom,
                    // Ajoutez d'autres attributs du professeur si nécessaire
                ],
                'note' => $note->notes, 
                'appreciation' => $note->commentaire,
                'evaluation' => $note->evaluation->type_evaluation,
                'evaluation_id' => $note->evaluation->id,
                'eleve' => [
                    'photo' => $eleve->photo,
                    'nom' =>  $eleve->nom,
                    'prenom' =>  $eleve->prenom,
                    'matricule' =>  $eleve->matricule,
                ],
                'classeEleve' => [
                    'id' => $classeEleve->id,
                ]
            ];
        }
    
        return response()->json([
            'message' => 'Liste des notes pour la classe du professeur spécifiée',
            'données' => $result,
            'status' => 200
        ]);
    }
    
    //note pour un eleve 
    public function noteEleve($classeEleve_id)
    {
        // Récupérer l'élève avec ses notes, évaluations et matières associées
        $classeEleve = ClasseEleve::with(['notes.evaluation.classeProf.profMatiere.matiere'])->find($classeEleve_id);
    
        // Vérifier si l'élève existe
        if (!$classeEleve) {
            return response()->json([
                'message' => 'Élève non trouvé.',
                'status' => 404
            ]);
        }
    
        // Vérifier si l'élève a des notes
        if ($classeEleve->notes->isEmpty()) {
            return response()->json([
                'message' => 'Aucune note trouvée pour cet élève.',
                'status' => 404
            ]);
        }
    
        // Préparer la réponse avec les notes de l'élève
        $result = [];
        foreach ($classeEleve->notes as $note) {
            $matiere = $note->evaluation->classeProf->profMatiere->matiere;
    
            $result[] = [
                'matiere' => $matiere->nom,
                'coefficient' => $matiere->coefficient,
                'note' => $note->notes,
                'commentaire' => $note->commentaire,
                'evaluation' => $note->evaluation->type_evaluation,
                'date' => $note->evaluation->date,
                'nom_evaluation' => $note->evaluation->nom,
            ];
        }
    
        // Retourner les notes de l'élève en JSON
        return response()->json([
            'message' => 'Notes de l\'élève',
            'eleve' => [
                'nom' => $classeEleve->eleve->nom,
                'prenom' => $classeEleve->eleve->prenom,
                'notes' => $result,
            ],
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
        try {
            // Créer une nouvelle note
            $note = Note::create([
                'notes' => $request->input('notes'),
                'commentaire' => $request->input('commentaire'),
                'evaluation_id' => $request->input('evaluation_id'),
                'classe_eleve_id' => $request->input('classe_eleve_id'),
                'bulletin_id' => $request->input('bulletin_id'),
            ]);
        
            // Structurer la réponse en JSON
            return response()->json([
                'message' => 'Note ajoutée avec succès.',
                'données' => $note,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout de la note.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
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
    try {
        // Mettre à jour les attributs de la note avec les données fournies
        $note->update([
            'notes' => $request->input('notes'),
            'commentaire' => $request->input('commentaire'),
            'evaluation_id' => $request->input('evaluation_id'),
            'classe_eleve_id' => $request->input('classe_eleve_id'),
            'bulletin_id' => $request->input('bulletin_id'),
        ]);

        // Structurer la réponse en JSON
        return response()->json([
            'message' => 'Note mise à jour avec succès.',
            'données' => $note,
            'status' => 200
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la mise à jour de la note.',
            'error' => $e->getMessage(),
            'status' => 500
        ]);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //supprimer une note
        //verifier si l'id existe
        if (Note::find($id)) {
            //supprimer la note
            Note::destroy($id);
            return response()->json([
               'message' => 'Note supprimée avec succès.',
               'status' => 200
            ]);
        } else {
            return response()->json([
               'message' => 'Note non trouvée.',
               'status' => 404
            ]);
        }
    }
}
