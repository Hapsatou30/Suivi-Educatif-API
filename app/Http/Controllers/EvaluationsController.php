<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Note;
use App\Models\Admin;
use App\Models\Parents;
use App\Models\Evaluations;
use App\Models\AnneeScolaire;
use App\Traits\NotificationTrait;
use App\Http\Requests\StoreEvaluationsRequest;
use App\Http\Requests\UpdateEvaluationsRequest;

class EvaluationsController extends Controller
{
    use NotificationTrait;
    /**
     * Liste des évaluations du jour 
     */
    public function evaluationsJour()
    {
        // Récupérer la date du jour
        $dateDuJour = now()->toDateString();

        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

        if (!$anneeEnCours) {
            return response()->json([
                'message' => "Aucune année scolaire en cours.",
                'status' => 404
            ]);
        }

        // Récupérer les évaluations pour la date du jour et l'année scolaire en cours
        $evaluations = Evaluations::with(['classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe', 'classeProf.anneeClasse.annee'])
            ->whereHas('classeProf.anneeClasse.annee', function ($query) use ($anneeEnCours) {
                $query->where('id', $anneeEnCours->id); // Filtrer par l'année en cours
            })
            ->whereDate('date', $dateDuJour)
            ->get();

        // Vérifier s'il y a des évaluations pour aujourd'hui dans l'année en cours
        if ($evaluations->isEmpty()) {
            return response()->json([
                'message' => "Il n'y a pas d'évaluations prévues pour aujourd'hui dans l'année en cours.",
                'status' => 200
            ]);
        }

        // Transformer les données pour afficher les informations souhaitées
        $resultat = $evaluations->map(function ($evaluation) {
            return [
                'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
                'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom . ' - ' . $evaluation->classeProf->profMatiere->professeur->nom,
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
        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

        if (!$anneeEnCours) {
            return response()->json([
                'message' => "Aucune année scolaire en cours.",
                'status' => 404
            ]);
        }

        // Récupérer les évaluations via les notes pour l'élève en filtrant par l'année scolaire en cours
        $notes = Note::with([
            'evaluation.classeProf.profMatiere.professeur',
            'evaluation.classeProf.profMatiere.matiere',
            'evaluation.classeProf.anneeClasse.classe'
        ])
            ->where('eleve_id', $eleveId)
            ->whereHas('evaluation.classeProf.anneeClasse.annee', function ($query) use ($anneeEnCours) {
                $query->where('id', $anneeEnCours->id); // Filtrer par l'année en cours
            })
            ->get();

        // Vérifier s'il y a des évaluations pour cet élève
        if ($notes->isEmpty()) {
            return response()->json([
                'message' => "Il n'y a pas d'évaluations pour cet élève dans l'année en cours.",
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
            'message' => "Évaluations pour l'élève dans l'année en cours",
            'evaluations' => $resultat,
            'status' => 200
        ]);
    }




    /**
     * Liste des évaluations par classe
     */
    public function index()
    {
        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

        if (!$anneeEnCours) {
            return response()->json([
                'message' => "Aucune année scolaire en cours.",
                'status' => 404
            ]);
        }

        // Récupérer les évaluations associées à l'année en cours avec les relations nécessaires
        $evaluations = Evaluations::with(['classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe'])
            ->whereHas('classeProf.anneeClasse.annee', function ($query) use ($anneeEnCours) {
                $query->where('id', $anneeEnCours->id); // Filtrer par l'année en cours
            })
            ->get();

        // Transformer les données pour afficher les informations souhaitées
        $resultat = $evaluations->map(function ($evaluation) {
            return [
                'id' => $evaluation->id,
                'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
                'nom' => $evaluation->nom,
                'date' => $evaluation->date,
                'heure' => $evaluation->heure,
                'duree' => $evaluation->duree,
                'classe' => $evaluation->classeProf->anneeClasse->classe->nom,
                'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom . ' ' . $evaluation->classeProf->profMatiere->professeur->nom,
                'profId' => $evaluation->classeProf->profMatiere->professeur->id,
            ];
        });

        return response()->json([
            'message' => 'Liste des évaluations de l\'année en cours',
            'données' => $resultat,
            'status' => 200
        ]);
    }






    /**
     * Ajouter une évaluation
     */
    public function store(StoreEvaluationsRequest $request)
    {
        try {
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

            // Créer l'évaluation
            $evaluation = Evaluations::create($data);

            // Récupérer la classe concernée par l'évaluation
            $classeProf = $evaluation->classeProf;
            $anneeClasse = $classeProf->anneeClasse;

            // Récupérer les élèves de cette classe
            $eleves = $anneeClasse->eleves;

            // Envoyer des notifications à chaque élève et parent
            foreach ($eleves as $eleve) {
                $parent = $eleve->parent;

                // Notification pour l'élève
                if ($eleve->user) {
                    $contenuNotificationEleve = "Une nouvelle évaluation a été ajoutée le " . $evaluation->date . " à " . $evaluation->heure . ".";
                    $this->sendNotification($eleve->user, $contenuNotificationEleve);
                }

                // Notification pour le parent
                if ($parent && $parent->user) {
                    $contenuNotificationParent = "Une nouvelle évaluation pour votre enfant " . $eleve->prenom . " est prévue le " . $evaluation->date . " à " . $evaluation->heure . ".";
                    $this->sendNotification($parent->user, $contenuNotificationParent);
                }
            }

            // Notification pour l'admin (on suppose qu'il n'y a qu'un seul admin)
            $admin = Admin::first(); // Ou bien récupérer tous les admins si nécessaire
            if ($admin && $admin->user) {
                $contenuNotificationAdmin = "Une nouvelle évaluation a été planifiée pour la classe " . $anneeClasse->classe->nom . " le " . $evaluation->date . " à " . $evaluation->heure . ".";
                $this->sendNotification($admin->user, $contenuNotificationAdmin);
            }

            return response()->json([
                'message' => 'Évaluation créée avec succès',
                'données' => $evaluation,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'évaluation.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
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
    public function evaluationsParProfesseur($professeurId)
    {
        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

        if (!$anneeEnCours) {
            return response()->json([
                'message' => "Aucune année scolaire en cours.",
                'status' => 404
            ]);
        }

        // Récupérer les évaluations avec les relations nécessaires pour un professeur spécifique
        $evaluations = Evaluations::with(['classeProf.profMatiere.matiere', 'classeProf.anneeClasse.classe'])
            ->whereHas('classeProf.profMatiere.professeur', function ($query) use ($professeurId) {
                $query->where('id', $professeurId);
            })
            ->whereHas('classeProf.anneeClasse.annee', function ($query) use ($anneeEnCours) {
                $query->where('id', $anneeEnCours->id); // Filtrer par l'année en cours
            })
            ->get();

        // Vérifier s'il y a des évaluations pour ce professeur
        if ($evaluations->isEmpty()) {
            return response()->json([
                'message' => "Il n'y a pas d'évaluations pour ce professeur.",
                'status' => 404
            ]);
        }

        // Transformer les données pour afficher les informations souhaitées
        $resultat = $evaluations->map(function ($evaluation) {
            return [
                'id' => $evaluation->id,
                'nom' => $evaluation->nom,
                'classe_prof_id' => $evaluation->classe_prof_id,
                'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
                'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom . ' ' . $evaluation->classeProf->profMatiere->professeur->nom,
                'type_evaluation' => $evaluation->type_evaluation,
                'date' => $evaluation->date,
                'heure' => $evaluation->heure,
                'duree' => $evaluation->duree,
                'classe' => $evaluation->classeProf->anneeClasse->classe->nom,
            ];
        });

        // Retourner les données sous forme de JSON
        return response()->json([
            'message' => "Évaluations pour le professeur",
            'evaluations' => $resultat,
            'status' => 200
        ]);
    }


    public function evaluationsParClasseProf($classeProfId)
    {
        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

        if (!$anneeEnCours) {
            return response()->json([
                'message' => "Aucune année scolaire en cours.",
                'status' => 404
            ]);
        }

        // Récupérer les évaluations pour un 'classeProf' spécifique avec les relations nécessaires
        $evaluations = Evaluations::with(['classeProf.profMatiere.matiere', 'classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe'])
            ->where('classe_prof_id', $classeProfId)
            ->whereHas('classeProf.anneeClasse.annee', function ($query) use ($anneeEnCours) {
                $query->where('id', $anneeEnCours->id); // Filtrer par l'année en cours
            })
            ->get();

        // Vérifier s'il y a des évaluations pour cette classeProf
        if ($evaluations->isEmpty()) {
            return response()->json([
                'message' => "Il n'y a pas d'évaluations pour cette classeProf.",
                'status' => 404
            ]);
        }

        // Transformer les données pour afficher les informations souhaitées
        $resultat = $evaluations->map(function ($evaluation) {
            return [
                'id' => $evaluation->id,
                'nom' => $evaluation->nom,
                'classe_prof_id' => $evaluation->classe_prof_id,
                'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
                'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom . ' ' . $evaluation->classeProf->profMatiere->professeur->nom,
                'type_evaluation' => $evaluation->type_evaluation,
                'date' => $evaluation->date,
                'heure' => $evaluation->heure,
                'duree' => $evaluation->duree,
                'classe' => $evaluation->classeProf->anneeClasse->classe->nom,
            ];
        });

        // Retourner les données sous forme de JSON
        return response()->json([
            'message' => "Évaluations pour la classe et le professeur",
            'evaluations' => $resultat,
            'status' => 200
        ]);
    }


    public function evaluationsParAnneeClasse($anneeClasseId)
    {
        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

        if (!$anneeEnCours) {
            return response()->json([
                'message' => "Aucune année scolaire en cours.",
                'status' => 404
            ]);
        }

        // Récupérer les évaluations pour une année classe spécifique avec les relations nécessaires
        $evaluations = Evaluations::with(['classeProf.profMatiere.matiere', 'classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe'])
            ->whereHas('classeProf.anneeClasse', function ($query) use ($anneeClasseId) {
                $query->where('id', $anneeClasseId);
            })
            ->whereHas('classeProf.anneeClasse.annee', function ($query) use ($anneeEnCours) {
                $query->where('id', $anneeEnCours->id); // Filtrer par l'année en cours
            })
            ->get();

        // Vérifier s'il y a des évaluations pour cette année classe
        if ($evaluations->isEmpty()) {
            return response()->json([
                'message' => "Il n'y a pas d'évaluations pour cette année classe.",
                'status' => 404
            ]);
        }

        // Transformer les données pour afficher les informations souhaitées
        $resultat = $evaluations->map(function ($evaluation) {
            return [
                'id' => $evaluation->id,
                'nom' => $evaluation->nom,
                'classe_prof_id' => $evaluation->classe_prof_id,
                'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
                'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom . ' ' . $evaluation->classeProf->profMatiere->professeur->nom,
                'type_evaluation' => $evaluation->type_evaluation,
                'date' => $evaluation->date,
                'heure' => $evaluation->heure,
                'duree' => $evaluation->duree,
                'classe' => $evaluation->classeProf->anneeClasse->classe->nom,
            ];
        });

        // Retourner les données sous forme de JSON
        return response()->json([
            'message' => "Évaluations pour l'année classe",
            'evaluations' => $resultat,
            'status' => 200
        ]);
    }


    //la liste des evaluations pour les élèves d'un parent


    public function evaluationsEleveParent($parentId)
    {
        // Récupérer les informations nécessaires (évaluations, élèves, matières)
        $parent = Parents::with([
            'eleves.anneeClasses' => function($query) {
                // Filtrer par année scolaire en cours
                $query->whereHas('annee', function($q) {
                    $q->where('etat', 'En_cours');
                });
            },
            'eleves.anneeClasses.classeProfs.evaluations',
            'eleves.anneeClasses.classeProfs.profMatiere.matiere' 
        ])->findOrFail($parentId);
    
        // Structure pour stocker les résultats
        $resultats = collect();
        
        // Parcourir les élèves, les classes et les évaluations
        foreach ($parent->eleves as $eleve) {
            foreach ($eleve->anneeClasses as $anneeClasse) {
                foreach ($anneeClasse->classeProfs as $classeProf) {
                    foreach ($classeProf->evaluations as $evaluation) {
                        $matiere = $classeProf->profMatiere->matiere;
                        
                        // Ajouter uniquement les informations nécessaires
                        $resultats->push([
                            'prenom' => $eleve->prenom, 
                            'matiere' => $matiere->nom, 
                            'type_evaluation' => $evaluation->type_evaluation, 
                            'date_evaluation' => $evaluation->date, 
                            'heure' => $evaluation->heure
                        ]);
                    }
                }
            }
        }
    
        return $resultats;
    }
    

public function evaluationsParClasseEleve($classeEleveId)
{
    // Récupérer l'année scolaire en cours
    $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

    // Récupérer les évaluations pour une classe élève spécifique avec les relations nécessaires
    $evaluations = Evaluations::with(['classeProf.profMatiere.matiere', 'classeProf.profMatiere.professeur', 'classeProf.anneeClasse.classe'])
        ->whereHas('classeProf.anneeClasse.eleves', function ($query) use ($classeEleveId) {
            $query->where('classe_eleves.id', $classeEleveId);
        })
        // Ajouter la condition pour filtrer par année scolaire en cours
        ->whereHas('classeProf.anneeClasse', function ($query) use ($anneeScolaireEnCours) {
            $query->where('annee_id', $anneeScolaireEnCours->id);
        })
        ->get();

    // Vérifier s'il y a des évaluations pour cette classe élève
    if ($evaluations->isEmpty()) {
        return response()->json([
            'message' => "Il n'y a pas d'évaluations pour cette classe élève.",
            'status' => 404
        ]);
    }

    // Transformer les données pour afficher les informations souhaitées
    $resultat = $evaluations->map(function ($evaluation) {
        return [
            'id' => $evaluation->id,
            'nom' => $evaluation->nom,
            'classe_prof_id' => $evaluation->classe_prof_id,
            'matiere' => $evaluation->classeProf->profMatiere->matiere->nom,
            'professeur' => $evaluation->classeProf->profMatiere->professeur->prenom . ' ' . $evaluation->classeProf->profMatiere->professeur->nom,
            'type_evaluation' => $evaluation->type_evaluation,
            'date' => $evaluation->date,
            'heure' => $evaluation->heure,
            'duree' => $evaluation->duree,
            'classe' => $evaluation->classeProf->anneeClasse->classe->nom,
        ];
    });

    // Retourner les données sous forme de JSON
    return response()->json([
        'message' => "Évaluations pour la classe élève",
        'evaluations' => $resultat,
        'status' => 200
    ]);
}

}
