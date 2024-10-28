<?php

namespace App\Http\Controllers;

use App\Models\Horaire;
use App\Models\ClasseProf;
use App\Models\AnneeScolaire;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreHoraireRequest;
use App\Http\Requests\UpdateHoraireRequest;

class HoraireController extends Controller
{
    use NotificationTrait; 
    /**
     * liste des horaire d'une classe
     */
    public function horaireClasse($anneeClasseId)
    {
        // Récupérer toutes les classes pour l'année donnée, même sans horaires
        $classeProfs = ClasseProf::where('annee_classe_id', $anneeClasseId)
            ->with(['horaires', 'anneeClasse.classe', 'profMatiere.professeur', 'profMatiere.matiere'])
            ->get();
    
        $data = [];
        foreach ($classeProfs as $classeProf) {
            if ($classeProf->horaires->isEmpty()) {
                // Si aucune horaire n'est associée, on indique "Pas encore rempli"
                $data[] = [
                    'nom_classe' => $classeProf->anneeClasse->classe->nom,
                    'matiere' => $classeProf->profMatiere->matiere->nom,
                    'professeur' => $classeProf->profMatiere->professeur->prenom . ' ' . $classeProf->profMatiere->professeur->nom,
                    'horaire' => 'Pas encore rempli',
                    'jour' => '-',
                    'horaire_id' => null, // Pas d'ID car pas encore d'horaire
                    'classe_prof_id' => $classeProf->id // Ajout de l'ID de la classe_prof
                ];
            } else {
                // Si des horaires existent, on les ajoute avec leur ID
                foreach ($classeProf->horaires as $horaire) {
                    $data[] = [
                        'horaire_id' => $horaire->id, // Récupération de l'ID de l'horaire
                        'nom_classe' => $classeProf->anneeClasse->classe->nom,
                        'matiere' => $classeProf->profMatiere->matiere->nom,
                        'professeur' => $classeProf->profMatiere->professeur->prenom . ' ' . $classeProf->profMatiere->professeur->nom,
                        'horaire' => $horaire->heure_debut . ' - ' . $horaire->heure_fin,
                        'jour' => $horaire->jour,
                        'classe_prof_id' => $classeProf->id // Ajout de l'ID de la classe_prof
                    ];
                }
            }
        }
    
        return response()->json([
            'message' => 'Liste des horaires pour l\'année classe',
            'données' => $data,
            'status' => 200
        ]);
    }
    
    
   
    //horaires pour un prof
    public function horaireProf($professeurId)
    {
        // Récupérer l'année scolaire en cours (état = 'En_cours')
        $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();
    
        if (!$anneeScolaireEnCours) {
            return response()->json([
                'message' => 'Aucune année scolaire en cours.',
                'status' => 404
            ]);
        }
    
        // Récupérer les horaires en fonction du professeur et de l'année scolaire en cours
        $horaires = Horaire::whereHas('classeProf.profMatiere', function ($query) use ($professeurId) {
            $query->where('professeur_id', $professeurId); 
        })
        ->whereHas('classeProf.anneeClasse', function ($query) use ($anneeScolaireEnCours) {
            // Filtrer par l'année scolaire en cours
            $query->where('annee_id', $anneeScolaireEnCours->id);
        })
        ->with([
            'classeProf', 
            'classeProf.profMatiere', 
            'classeProf.profMatiere.professeur', 
            'classeProf.profMatiere.matiere', 
            'classeProf.anneeClasse.classe'
        ]) // Charger les relations nécessaires
        ->get(); 
    
        // Vérifier s'il y a des horaires pour ce professeur
        if ($horaires->isEmpty()) {
            return response()->json([
                'message' => 'Aucun horaire trouvé pour ce professeur.',
                'status' => 404
            ]);
        }
    
        // Formater les données pour inclure les noms de classe et de matière
        $horairesFormates = $horaires->map(function ($horaire) {
            return [
                'id' => $horaire->id,
                'jour' => $horaire->jour,
                'heure_debut' => $horaire->heure_debut,
                'heure_fin' => $horaire->heure_fin,
                'classe' => $horaire->classeProf->anneeClasse->classe->nom, 
                'nom_prof' => $horaire->classeProf->profMatiere->professeur->nom,
                'prenom_prof' => $horaire->classeProf->profMatiere->professeur->prenom,
                'nom_matiere' => $horaire->classeProf->profMatiere->matiere->nom,
                'classe_prof_id' => $horaire->classeProf->id, 
            ];
        });
    
        return response()->json([
            'message' => 'Liste des horaires pour le professeur',
            'données' => $horairesFormates,
            'status' => 200
        ]);
    }
    

    

    /**
     * Methode  ajouter une horaire
     */
   public function store(StoreHoraireRequest $request)
{
    $classe_prof_id = $request->input('classe_prof_id');
    $jour = $request->input('jour');  // Le jour de la semaine
    $heure_debut = $request->input('heure_debut');
    $heure_fin = $request->input('heure_fin');

    // Récupérer l'objet ClasseProf
    $classeProf = ClasseProf::with(['profMatiere.professeur', 'anneeClasse'])->find($classe_prof_id);

    if (!$classeProf) {
        return response()->json([
            'error' => 'Classe professeur non trouvé.',
        ], 404);
    }

    // Récupérer l'ID du professeur
    $professeurId = $classeProf->profMatiere->professeur->id; // ID du professeur
    $professeur = $classeProf->profMatiere->professeur; // Récupérer l'objet professeur

    // Récupérer l'ID de anneeClasse
    $anneeClasseId = $classeProf->anneeClasse->id;

    // Vérifier les conflits d'horaires pour cette classe
    $conflitHoraire = Horaire::where('classe_prof_id', $classe_prof_id)
        ->where('jour', $jour)
        ->where(function($query) use ($heure_debut, $heure_fin) {
            $query->where(function($q) use ($heure_debut, $heure_fin) {
                    $q->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                      ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                      ->orWhere(function($subQuery) use ($heure_debut, $heure_fin) {
                          $subQuery->where('heure_debut', '=', $heure_debut)
                                    ->where('heure_fin', '>=', $heure_debut)
                                    ->where('heure_fin', '<=', $heure_fin);
                      });
                })
                ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                    $q->where('heure_debut', '<', $heure_debut)
                      ->where('heure_fin', '>', $heure_fin);
                });
        })
        ->exists(); // Vérifier si un conflit d'horaire existe

    if ($conflitHoraire) {
        return response()->json([
            'error' => 'Cette classe est déjà occupée à cet horaire.',
        ], 400);
    }

    // Vérifier la disponibilité de la classe pour l'année concernée
    $conflitAnneeClasse = Horaire::whereHas('classeProf', function($query) use ($anneeClasseId) {
            $query->where('annee_classe_id', $anneeClasseId);
        })
        ->where('jour', $jour)
        ->where(function($query) use ($heure_debut, $heure_fin) {
            $query->where(function($q) use ($heure_debut, $heure_fin) {
                    $q->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                      ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                      ->orWhere(function($subQuery) use ($heure_debut, $heure_fin) {
                          $subQuery->where('heure_debut', '=', $heure_debut)
                                    ->where('heure_fin', '>=', $heure_debut)
                                    ->where('heure_fin', '<=', $heure_fin);
                      });
                })
                ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                    $q->where('heure_debut', '<', $heure_debut)
                      ->where('heure_fin', '>', $heure_fin);
                });
        })
        ->exists(); // Vérifier si un conflit d'horaire existe pour l'année de classe

    if ($conflitAnneeClasse) {
        return response()->json([
            'error' => 'Cette classe est déjà occupée à cet horaire dans l\'année concernée.',
        ], 400);
    }

    // Vérifier si le professeur est occupé à cette heure pour toutes les classes
    $conflitProf = Horaire::whereHas('classeProf', function($query) use ($professeurId) {
            $query->whereHas('profMatiere', function($subQuery) use ($professeurId) {
                // Vérifier si le professeur est lié à la classe
                $subQuery->where('professeur_id', $professeurId);
            });
        })
        ->where('jour', $jour)
        ->where(function($query) use ($heure_debut, $heure_fin) {
            $query->where(function($q) use ($heure_debut, $heure_fin) {
                    $q->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                      ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                      ->orWhere(function($subQuery) use ($heure_debut, $heure_fin) {
                          $subQuery->where('heure_debut', '=', $heure_debut)
                                    ->where('heure_fin', '>=', $heure_debut)
                                    ->where('heure_fin', '<=', $heure_fin);
                      });
                })
                ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                    $q->where('heure_debut', '<', $heure_debut)
                      ->where('heure_fin', '>', $heure_fin);
                });
        })
        ->exists(); // Vérifier si un conflit d'horaire existe pour le professeur

    if ($conflitProf) {
        return response()->json([
            'error' => 'Ce professeur est déjà occupé à cet horaire dans une autre classe.',
        ], 400);
    }

    // Si pas de conflit, on peut créer l'horaire
    $horaire = Horaire::create($request->all());
    Log::info('Données reçues:', $request->all());

    // Envoyer une notification au professeur
    $contenuNotification = "Un nouvel horaire a été ajouté pour la classe " . $classeProf->anneeClasse->classe->nom . " le " . $jour . " de " . $heure_debut . " à " . $heure_fin;
    $this->sendNotification($professeur->user, $contenuNotification);

    return response()->json([
        'message' => 'Horaire créé avec succès.',
        'données' => $horaire,
        'status' => 201
    ]);
}

    
    
    

    /**
     * Display the specified resource.
     */
    public function show(Horaire $horaire)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHoraireRequest $request, $id)
    {
        try {
            // Récupérer l'horaire actuel
            $horaire = Horaire::findOrFail($id);
    
            // Récupérer les données de la requête
            $classe_prof_id = $request->input('classe_prof_id');
            $jour = $request->input('jour');
            $heure_debut = $request->input('heure_debut');
            $heure_fin = $request->input('heure_fin');
    
            // Récupérer l'objet ClasseProf
            $classeProf = ClasseProf::with(['profMatiere.professeur', 'anneeClasse'])->find($classe_prof_id);
    
            if (!$classeProf) {
                return response()->json([
                    'error' => 'Classe professeur non trouvé.',
                ], 404);
            }
    
            // Récupérer l'ID du professeur
            $professeurId = $classeProf->profMatiere->professeur->id;
            $anneeClasseId = $classeProf->anneeClasse->id;
    
            // Vérifier les conflits d'horaires pour cette classe
            $conflitHoraire = Horaire::where('classe_prof_id', $classe_prof_id)
                ->where('jour', $jour)
                ->where('id', '!=', $id) // Exclure l'horaire actuel
                ->where(function($query) use ($heure_debut, $heure_fin) {
                    $query->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                          ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                          ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                              $q->where('heure_debut', '<', $heure_debut)
                                ->where('heure_fin', '>', $heure_fin);
                          });
                })
                ->exists();
    
            if ($conflitHoraire) {
                return response()->json([
                    'error' => 'Cette classe est déjà occupée à cet horaire.',
                ], 400);
            }
    
            // Vérifier les conflits pour l'année de la classe
            $conflitAnneeClasse = Horaire::whereHas('classeProf', function($query) use ($anneeClasseId) {
                    $query->where('annee_classe_id', $anneeClasseId);
                })
                ->where('jour', $jour)
                ->where('id', '!=', $id) // Exclure l'horaire actuel
                ->where(function($query) use ($heure_debut, $heure_fin) {
                    $query->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                          ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                          ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                              $q->where('heure_debut', '<', $heure_debut)
                                ->where('heure_fin', '>', $heure_fin);
                          });
                })
                ->exists();
    
            if ($conflitAnneeClasse) {
                return response()->json([
                    'error' => 'Cette classe est déjà occupée à cet horaire dans l\'année concernée.',
                ], 400);
            }
    
            // Vérifier si le professeur est occupé à cette heure pour toutes les classes
            $conflitProf = Horaire::whereHas('classeProf', function($query) use ($professeurId) {
                    $query->whereHas('profMatiere', function($subQuery) use ($professeurId) {
                        $subQuery->where('professeur_id', $professeurId);
                    });
                })
                ->where('jour', $jour)
                ->where('id', '!=', $id) // Exclure l'horaire actuel
                ->where(function($query) use ($heure_debut, $heure_fin) {
                    $query->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                          ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                          ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                              $q->where('heure_debut', '<', $heure_debut)
                                ->where('heure_fin', '>', $heure_fin);
                          });
                })
                ->exists();
    
            if ($conflitProf) {
                return response()->json([
                    'error' => 'Ce professeur est déjà occupé à cet horaire dans une autre classe.',
                ], 400);
            }
    
            // Si pas de conflit, on peut mettre à jour l'horaire
            $horaire->update($request->validated());
    
            // Envoyer une notification au professeur concernant la modification
            $contenuNotification = "L'horaire a été modifié pour la classe " . $classeProf->anneeClasse->classe->nom . " le " . $jour . " de " . $heure_debut . " à " . $heure_fin;
            $this->sendNotification($classeProf->profMatiere->professeur->user, $contenuNotification);
    
            return response()->json([
                'message' => 'Horaire modifié avec succès',
                'données' => $horaire,
                'status' => 200
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la modification de l\'horaire',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //supprimer une horaire
        $horaire = Horaire::find($id);
        $horaire->delete();
        return response()->json([
           'message' => 'Horaire supprimé avec succès',
           'status' => 204
        ]);
    }
}
