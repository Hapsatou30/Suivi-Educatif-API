<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Eleve;
use App\Models\Bulletin;
use App\Models\ClasseEleve;
use App\Models\AnneeScolaire;
use App\Traits\NotificationTrait;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;

class NoteController extends Controller
{
    use NotificationTrait;
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
    
        // Préparer la réponse avec les notes groupées par période
        $result = [];
    
        foreach ($notes as $note) {
            // Récupérer les informations sur la matière et le professeur
            $matiere = $note->evaluation->classeProf->profMatiere->matiere;
            $professeur = $note->evaluation->classeProf->profMatiere->professeur;
            $eleve = $note->bulletin->classeEleve->eleve;
            $bulletin = $note->bulletin;
    
            // Initialiser la clé de la période si elle n'existe pas encore dans $result
            $periode = $bulletin->periode;
            if (!isset($result[$periode])) {
                $result[$periode] = [];
            }
    
            // Ajouter les informations de la note dans la période correspondante
            $result[$periode][] = [
                'id' => $note->id,
                'matiere' => $matiere->nom,
                'professeur' => [
                    'nom' => $professeur->nom,
                    'prenom' => $professeur->prenom,
                ],
                'note' => $note->notes,
                'appreciation' => $note->commentaire,
                'evaluation' => $note->evaluation->type_evaluation,
                'evaluation_id' => $note->evaluation->id,
                'bulletin_id' => [
                    'id' => $bulletin->id,
                    'periode' => $bulletin->periode,
                    'classe_eleve_id' => $bulletin->classe_eleve_id,
                ],
                'eleve' => [
                    'photo' => $eleve->photo,
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                    'matricule' => $eleve->matricule,
                ],
            ];
        }
    
        return response()->json([
            'message' => 'Liste des notes pour la classe du professeur spécifiée',
            'données' => $result, // Les notes sont maintenant groupées par période
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
        // Récupérer l'année scolaire en cours
        $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();
    
        if (!$anneeScolaireEnCours) {
            return response()->json([
                'message' => 'Aucune année scolaire en cours.',
                'status' => 404
            ]);
        }
    
        // Récupérer les élèves du parent
        $eleves = Eleve::where('parent_id', $parent_id)
            ->with(['classeEleves.bulletins.notes.evaluation.classeProf.profMatiere.matiere', 'classeEleves.bulletins.notes.evaluation.classeProf.anneeClasse' => function ($query) use ($anneeScolaireEnCours) {
                // Filtrer par année scolaire en cours
                $query->whereHas('classeEleves.bulletins.notes.evaluation.classeProf.anneeClasse.annee', function ($q) use ($anneeScolaireEnCours) {
                    $q->where('annee_id', $anneeScolaireEnCours->id);
                });
            }])
            ->get();
    
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
                foreach ($classeEleve->bulletins as $bulletin) {
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
    

    public function notesParAnneeClasse($anneeClasse_id)
    {
        // Récupérer l'année scolaire en cours (où 'etat' = 'En_cours')
        $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();
    
        if (!$anneeScolaireEnCours) {
            return response()->json([
                'message' => 'Aucune année scolaire en cours.',
                'status' => 404
            ]);
        }
    
        // Récupérer toutes les notes pour une classe donnée d'une année spécifique,
        // en chargeant les relations nécessaires pour les évaluations, matières et élèves
        $notes = Note::with([
            'evaluation.classeProf.profMatiere.matiere',
            'bulletin.classeEleve.eleve'
        ])
        ->whereHas('bulletin.classeEleve.anneeClasse', function ($query) use ($anneeClasse_id, $anneeScolaireEnCours) {
            // Filtrer les notes en fonction de l'ID de l'année classe et l'année scolaire en cours
            $query->where('annee_classe_id', $anneeClasse_id)
                  ->where('annee_id', $anneeScolaireEnCours->id);
        })
        ->get();
    
        // Si aucune note n'est trouvée, retourner une réponse JSON avec un statut 404
        if ($notes->isEmpty()) {
            return response()->json([
                'message' => 'Aucune note trouvée pour cette classe.',
                'status' => 404
            ]);
        }
    
        // Initialisation du tableau qui contiendra les résultats par période, matière et élève
        $result = [];
    
        // Parcourir chaque note pour structurer les données
        foreach ($notes as $note) {
            // Récupérer la matière, l'élève et la période associée
            $matiere = $note->evaluation->classeProf->profMatiere->matiere;
            $eleve = $note->bulletin->classeEleve->eleve;
            $bulletin = $note->bulletin;
            $periode = $bulletin->periode;
    
            // Si la période n'existe pas encore dans le tableau, on l'ajoute
            if (!isset($result[$periode])) {
                $result[$periode] = [];
            }
    
            // Si la matière n'existe pas encore dans la période, on l'ajoute
            if (!isset($result[$periode][$matiere->nom])) {
                $result[$periode][$matiere->nom] = [
                    'coefficient' => $matiere->coefficient,
                    'eleves' => []
                ];
            }
    
            // Si l'élève n'est pas encore ajouté à la matière, on l'ajoute avec ses infos
            if (!isset($result[$periode][$matiere->nom]['eleves'][$eleve->matricule])) {
                $result[$periode][$matiere->nom]['eleves'][$eleve->matricule] = [
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom,
                    'matricule' => $eleve->matricule,
                    'notes' => [
                        'devoirs' => [],
                        'examens' => []
                    ]
                ];
            }
    
            // Ajouter la note à la liste correspondante (devoirs ou examens)
            if (stripos($note->evaluation->type_evaluation, 'Devoir') !== false) {
                $result[$periode][$matiere->nom]['eleves'][$eleve->matricule]['notes']['devoirs'][] = $note->notes;
            } else {
                $result[$periode][$matiere->nom]['eleves'][$eleve->matricule]['notes']['examens'][] = $note->notes;
            }
        }
    
        // Calcul des moyennes des devoirs et examens pour chaque élève par période et matière
        foreach ($result as $periode => $matieres) {
            foreach ($matieres as $matiere => $data) {
                foreach ($data['eleves'] as $key => $eleve) {
                    // Calcul de la moyenne des devoirs et examens
                    $moyenneDevoirs = !empty($eleve['notes']['devoirs']) ? array_sum($eleve['notes']['devoirs']) / count($eleve['notes']['devoirs']) : null;
                    $moyenneExamens = !empty($eleve['notes']['examens']) ? array_sum($eleve['notes']['examens']) / count($eleve['notes']['examens']) : null;
    
                    // Calcul de la moyenne globale
                    if ($moyenneDevoirs !== null && $moyenneExamens !== null) {
                        $moyenneGlobale = round(($moyenneDevoirs + $moyenneExamens) / 2, 2);
                        $result[$periode][$matiere]['eleves'][$key]['notes']['moyenne_globale'] = $moyenneGlobale;
                    } else {
                        $result[$periode][$matiere]['eleves'][$key]['notes']['moyenne_globale'] = null;
                    }
    
                    // Nettoyer les notes après calcul
                    unset($result[$periode][$matiere]['eleves'][$key]['notes']['devoirs']);
                    unset($result[$periode][$matiere]['eleves'][$key]['notes']['examens']);
                }
            }
        }
    
        // Réindexer le tableau des élèves pour chaque matière et chaque période
        foreach ($result as $periode => $matieres) {
            foreach ($matieres as $matiere => $data) {
                $result[$periode][$matiere]['eleves'] = array_values($data['eleves']);
            }
        }
    
        // Retourner une réponse JSON avec les données regroupées par période
        return response()->json([
            'message' => 'Liste des notes par période, matière et élève pour la classe spécifiée.',
            'données' => $result,
            'status' => 200
        ]);
    }
    
    

}
