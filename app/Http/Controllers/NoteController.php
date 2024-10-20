<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Eleve;
use App\Models\Bulletin;
use App\Models\ClasseEleve;
use App\Traits\NotificationTrait;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
class NoteController extends Controller
{   use NotificationTrait; 
    /**
     * Liste des notes par matiere
     */
    public function index($classe_prof_id)
    {
        // Récupérer les notes pour la classe du professeur associée à un ID de classe_prof
        $notes = Note::with([
            'evaluation.classeProf.profMatiere.matiere',
            'evaluation.classeProf.profMatiere.professeur',
            'bulletin.classeEleve.eleve'
        ])
            ->whereHas('evaluation', function ($query) use ($classe_prof_id) {
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
            $professeur = $note->evaluation->classeProf->profMatiere->professeur; 

            $eleve = $note->bulletin->classeEleve->eleve;
            $bulletin = $note->bulletin;
            $classeEleve = $note->bulletin->classeEleve; 

            $result[] = [
                'id' => $note->id,
                'matiere' => $matiere->nom,
                'professeur' => [
                    'nom' => $professeur->nom,
                    'prenom' => $professeur->prenom,
                ],
                'note' => $note->notes,
                'appreciation' => $note->commentaire,
                'evaluation' => $note->evaluation->nom,
                'evaluation_id' => $note->evaluation->id,
                'bulletin_id' =>[
                    'id' => $bulletin->id,
                    'periode' => $bulletin->periode,
                    'classe_eleve_id' => $bulletin->classe_eleve_id,

                ],
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
        // Récupérer la classe élève correspondante
        $classeEleve = ClasseEleve::with('eleve', 'bulletins.notes.evaluation.classeProf.profMatiere.matiere')
                        ->find($classeEleve_id);
    
        // Si la classe élève n'existe pas, retourner une erreur
        if (!$classeEleve) {
            return response()->json([
                'message' => 'Classe élève non trouvée',
                'status' => 404
            ]);
        }
    
        // Initialiser la liste des résultats
        $result = [];
    
        // Parcourir les bulletins liés à la classe élève
        foreach ($classeEleve->bulletins as $bulletin) {
            // Vérifier si le bulletin a des notes
            if ($bulletin->notes->isNotEmpty()) {
                // Parcourir chaque note dans le bulletin
                foreach ($bulletin->notes as $note) {
                    // Récupérer la matière associée à l'évaluation
                    $matiere = $note->evaluation->classeProf->profMatiere->matiere;
    
                    // Ajouter les détails de la note à la réponse
                    $result[] = [
                        'matiere' => $matiere->nom,
                        'coefficient' => $matiere->coefficient,
                        'note' => $note->notes,  
                        'commentaire' => $note->commentaire,
                        'evaluation' => $note->evaluation->type_evaluation,
                        'date' => $note->evaluation->date,
                        'nom_evaluation' => $note->evaluation->nom,
                        'bulletin_id' => $bulletin->id,  
                        'periode' => $bulletin->periode 
                    ];
                }
            }
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
                'bulletin_id' => $request->input('bulletin_id'),
                'periode' => $request->input('periode'), 
            ]);
    
            // Récupérer l'élève à partir du bulletin
            $bulletin = Bulletin::find($note->bulletin_id);
            $classeEleve = ClasseEleve::where('id', $bulletin->classe_eleve_id)->first(); // Modifiez cette ligne en fonction de votre clé étrangère
    
            if ($classeEleve) {
                $eleve = $classeEleve->eleve;
    
                // Vérifier si l'élève et son parent existent
                if ($eleve && $eleve->parent && $eleve->parent->user) {
                    // Contenu des notifications
                    $contenuNotificationEleve = "Une nouvelle note a été ajoutée : " . $note->notes . " pour l'évaluation " . $note->evaluation->nom . " de la matière " . $note->evaluation->classeProf->profMatiere->matiere->nom;
                    $contenuNotificationParent = "Votre enfant " . $eleve->prenom . " a reçu une nouvelle note : " . $note->notes . " pour l'évaluation " . $note->evaluation->nom . " de la matière " . $note->evaluation->classeProf->profMatiere->matiere->nom;
    
                    // Envoyer la notification à l'élève
                    $this->sendNotification($eleve->user, $contenuNotificationEleve);
    
                    // Envoyer la notification au parent
                    $this->sendNotification($eleve->parent->user, $contenuNotificationParent);
                }
            }
    
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
            // Préparer un tableau avec les champs présents dans la requête
            $dataToUpdate = array_filter($request->only(['notes', 'commentaire', 'evaluation_id', 'bulletin_id', 'periode']), function ($value) {
                return !is_null($value); // Exclure les valeurs nulles
            });
    
            // Mettre à jour la note avec les champs valides
            $note->update($dataToUpdate);
    
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
    public function destroy($id)
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

    public function notesParParent($parent_id)
    {
        // Récupérer les élèves du parent
        $eleves = Eleve::where('parent_id', $parent_id)
                        ->with('classeEleves.bulletins.notes.evaluation.classeProf.profMatiere.matiere')
                        ->get();
    
        // Vérifier si des élèves existent pour ce parent
        if ($eleves->isEmpty()) {
            return response()->json([
                'message' => 'Aucun élève trouvé pour ce parent.',
                'status' => 404
            ]);
        }
    
        // Préparer la réponse avec la liste des notes pour chaque élève du parent
        $result = [];
        foreach ($eleves as $eleve) {
            foreach ($eleve->classeEleves as $classeEleve) {
                // Parcourir chaque bulletin de la classe de l'élève
                foreach ($classeEleve->bulletins as $bulletin) {
                    // Parcourir les notes de chaque bulletin
                    foreach ($bulletin->notes as $note) {
                        $matiere = $note->evaluation->classeProf->profMatiere->matiere;
    
                        $result[] = [
                            'eleve' => [
                                'nom' => $eleve->nom,
                                'prenom' => $eleve->prenom,
                                'matricule' => $eleve->matricule,
                            ],
                            'matiere' => $matiere->nom,
                            'note' => $note->notes, 
                            'coefficient' => $matiere->coefficient,
                            'commentaire' => $note->commentaire,
                            'evaluation' => $note->evaluation->type_evaluation,
                            'date' => $note->evaluation->date,
                            'nom_evaluation' => $note->evaluation->nom,
                        ];
                    }
                }
            }
        }
    
        return response()->json([
            'message' => 'Liste des notes pour les enfants du parent spécifié',
            'données' => $result,
            'status' => 200
        ]);
    }
    
    
}
