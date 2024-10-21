<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Parents;
use App\Models\AnneeClasse;
use App\Models\ClasseEleve;
use App\Models\AnneeScolaire;
use App\Http\Requests\StoreClasseEleveRequest;
use App\Http\Requests\UpdateClasseEleveRequest;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Auth;

class ClasseEleveController extends Controller
{
     use NotificationTrait; 
    public function classeEleve()
    {
        $classeEleve = ClasseEleve::all();
        return response()->json ([
           'message' => 'Liste des Eleve qui ont une classe',
           'données' => $classeEleve,
           'status' => 200
        ]);
    }

  
    public function totalEleves()
{
    // Compter le nombre d'élèves pour l'année en cours
    $nombreElevesTotal = Eleve::whereHas('anneeClasses', function ($query) {
        $query->whereHas('annee', function ($query) {
            $query->where('etat', 'En_cours'); // Filtrer par état "En_cours"
        });
    })->count();

    // Structurer la réponse en JSON
    return response()->json([
        'message' => 'Nombre total d\'élèves pour l\'année en cours',
        'nombre_eleves_total' => $nombreElevesTotal,
        'status' => 200
    ]);
}

    /**
     * les eleves par annee_classe
     */
    public function index($anneeClasseId)
    {
        // Récupérer l'année de classe spécifique avec ses élèves
        $anneeClasse = AnneeClasse::with(['classe', 'eleves.anneeClasses', 'eleves.classeEleves.bulletins']) 
            ->where('id', $anneeClasseId)
            ->whereHas('annee', function ($query) {
                $query->where('etat', 'En_cours');
            })
            ->first();
    
        // Vérifier si l'année de classe existe
        if (!$anneeClasse) {
            return response()->json([
                'message' => 'Année de classe non trouvée.',
                'status' => 404
            ], 404);
        }
    
        // Structurer la réponse en JSON
        return response()->json([
            'message' => 'Liste des élèves pour l\'année de classe demandée',
            'données' => [
                [
                    'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
                    'classe' => $anneeClasse->classe->nom,
                    'id_anneeClasse' => $anneeClasse->id,
                    'id_classe' => $anneeClasse->classe->id,
                    'eleves' => $anneeClasse->eleves->map(function ($eleve) {
                        return [
                            'id_eleve' => $eleve->id,
                            'nom' => $eleve->nom,
                            'prenom' => $eleve->prenom,
                            'matricule' => $eleve->matricule,
                            'id_classeEleve' => $eleve->classeEleves->first()->id ?? null, // Obtenir le premier id de ClasseEleve
                            'date_naissance' => $eleve->date_naissance,
                            'telephone' => $eleve->telephone,
                            'genre' => $eleve->genre,
                            'photo' => $eleve->photo,
                            'parent' => $eleve->parent->prenom . ' ' . $eleve->parent->nom,
                            'parent_telephone' => $eleve->parent->telephone,
                            'bulletins' => $eleve->classeEleves->flatMap(function ($classeEleve) {
                                return $classeEleve->bulletins->map(function ($bulletin) {
                                    return [
                                        'id_bulletin' => $bulletin->id,
                                        'periode' => $bulletin->periode,
                                    ];
                                });
                            }),
                        ];
                    }),
                ],
            ],
            'status' => 200
        ]);
    }
    

//liste des eleves et leur annees classes regroupée par parent
    /**
 * Méthode pour récupérer la liste des élèves et leur année de classe regroupée par parent
 */
public function elevesParParent($parent_id)
{
    // Récupérer le parent
    $parent = Parents::with('eleves.anneeClasses.classe')->find($parent_id);

    // Vérifier si le parent existe
    if (!$parent) {
        return response()->json([
            'message' => 'Parent non trouvé.',
            'status' => 404
        ]);
    }

    // Vérifier si le parent a des élèves associés
    if ($parent->eleves->isEmpty()) {
        return response()->json([
            'message' => 'Aucun élève trouvé pour ce parent.',
            'status' => 404
        ]);
    }

    // Préparer la liste des élèves avec leurs informations
    $eleves = $parent->eleves->map(function ($eleve) {
        return [
            
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'photo' => $eleve->photo,
            'annee_classe' => $eleve->anneeClasses->map(function ($anneeClasse) {
                return [
                    'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
                    'classe' => $anneeClasse->classe->nom,
                    'classeEleve_id' => $anneeClasse->pivot->id, 
                    
                ];
            })
        ];
    });

    return response()->json([
        'message' => 'Liste des élèves pour le parent spécifié',
        'données' => $eleves,
        'status' => 200
    ]);
}

//nombre eleve par parent
public function nombreElevesParParent($parent_id)
{
    // Récupérer le parent avec les élèves associés
    $parent = Parents::with('eleves.anneeClasses')->find($parent_id);

    // Vérifier si le parent existe
    if (!$parent) {
        return response()->json([
            'message' => 'Parent non trouvé.',
            'status' => 404
        ]);
    }

    // Compter le nombre d'élèves associés à ce parent
    $nombreEleves = $parent->eleves->count();

    // Retourner la réponse en JSON
    return response()->json([
        'message' => 'Nombre d\'élèves pour le parent spécifié.',
        'parent' => [
            'nom' => $parent->nom,
            'prenom' => $parent->prenom,
            'nombre_eleves' => $nombreEleves,
        ],
        'status' => 200
    ]);
}


    /**
     * Attribuer des classes aux eleves
     */
    public function store(StoreClasseEleveRequest $request)
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Vérifier si l'année associée est "En_cours"
        $anneeClasse = AnneeClasse::find($validated['annee_classe_id']);
        if ($anneeClasse->annee->etat !== 'En_cours') {
            return response()->json([
                'message' => 'L\'année scolaire doit être en cours pour ajouter un élève.',
                'status' => 400
            ], 400);
        }
        
        // Si 'eleve_id' est un tableau d'IDs
        if (is_array($validated['eleve_id'])) {
            $classeEleves = [];
            
            // Boucler sur chaque 'eleve_id' et créer une entrée
            foreach ($validated['eleve_id'] as $eleve_id) {
                $classeEleve = ClasseEleve::create([
                    'annee_classe_id' => $validated['annee_classe_id'],
                    'eleve_id' => $eleve_id,
                ]);
                
                // Récupérer l'élève
                $eleve = Eleve::with('parent.user', 'user')->find($eleve_id);
                
                // Envoyer une notification à l'élève
                if ($eleve && $eleve->user) {
                    $contenuNotification = "Vous avez été attribué à la classe : " . $anneeClasse->classe->nom;
                    $this->sendNotification($eleve->user, $contenuNotification);
                }
                
                // Envoyer une notification au parent
                if ($eleve && $eleve->parent && $eleve->parent->user) {
                    $contenuNotificationParent = "Votre enfant " . $eleve->prenom. " a été attribué à la classe : " . $anneeClasse->classe->nom;
                    $this->sendNotification($eleve->parent->user, $contenuNotificationParent);
                }
    
                $classeEleves[] = $classeEleve;
            }
            
            // Retourner une réponse JSON avec les élèves ajoutés
            return response()->json([
                'message' => 'Élèves attribués à l\'année classe avec succès',
                'data' => $classeEleves,
                'status' => 201
            ]);
            
        } else {
            // Gérer le cas où 'eleve_id' est une valeur unique
            $classeEleve = ClasseEleve::create([
                'annee_classe_id' => $validated['annee_classe_id'],
                'eleve_id' => $validated['eleve_id'],
            ]);
            
            // Récupérer l'élève
            $eleve = Eleve::with('parent.user', 'user')->find($validated['eleve_id']);
            
            // Envoyer une notification à l'élève
            if ($eleve && $eleve->user) {
                $contenuNotification = "Vous avez été attribué à la classe : " . $anneeClasse->classe->nom;
                $this->sendNotification($eleve->user, $contenuNotification);
            }
            
            // Envoyer une notification au parent
            if ($eleve && $eleve->parent && $eleve->parent->user) {
                $contenuNotificationParent = "Votre enfant " . $eleve->prenom . " a été attribué à la classe : " . $anneeClasse->classe->nom;
                $this->sendNotification($eleve->parent->user, $contenuNotificationParent);
            }            
    
            // Retourner une réponse JSON pour un seul élève
            return response()->json([
                'message' => 'Élève attribué à l\'année classe avec succès',
                'data' => $classeEleve,
                'status' => 201
            ]);
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(ClasseEleve $classeEleve)
    {
        
        $eleve = $classeEleve->eleve;
        
        
        $anneeClasseId = $classeEleve->anneeClasse->id;
        $classe = $classeEleve->anneeClasse->classe;
        $anneeScolaire = $classeEleve->anneeClasse->annee;
    
        return response()->json([
            'message' => 'Détails de l\'élève',
            'données' => [
                'classeEleve_id' => $classeEleve->id,
                'anneeClasse_id' => $anneeClasseId,  
                'anneeScolaire' => $anneeScolaire->annee_debut . ' - '. $anneeScolaire->annee_fin,
                'prenom' => $eleve->prenom,
                'nom' => $eleve->nom,
                'sexe' => $eleve->genre,
                'matricule' => $eleve->matricule,
                'dateNaissance' => $eleve->date_naissance,
                'classe' => $classe->nom,
                'niveau' => $classe->niveau
            ],
            'status' => 200
        ]);
    }

    /**
 * Récupérer les informations de l'élève et sa classe en utilisant l'ID de l'élève
 *
 */
public function getEleveDetails()
{
    // Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Vérifier si l'utilisateur est bien un élève
    if (!$user || !$user->eleve) {
        return response()->json([
            'message' => 'Aucun élève connecté.',
            'status' => 404
        ], 404);
    }

    // Récupérer l'élève connecté
    $eleve = $user->eleve;

    // Récupérer la classe de l'élève pour l'année scolaire en cours
    $classeEleve = $eleve->classeEleves()->whereHas('anneeClasse.annee', function ($query) {
        $query->where('etat', 'En_cours');
    })->with('anneeClasse.classe', 'anneeClasse.annee')->first();

    // Vérifier si l'élève a une classe pour l'année en cours
    if (!$classeEleve) {
        return response()->json([
            'message' => 'Aucune classe trouvée pour l\'élève connecté durant l\'année en cours.',
            'status' => 404
        ], 404);
    }

    // Structurer la réponse avec les détails de l'élève et de la classe
    return response()->json([
        'message' => 'Détails de l\'élève connecté',
        'données' => [
            'prenom' => $eleve->prenom,
            'nom' => $eleve->nom,
            'matricule' => $eleve->matricule,
            'photo' => $eleve->photo,
            'classe' => $classeEleve->anneeClasse->classe->nom,
            'niveau' => $classeEleve->anneeClasse->classe->niveau,
            'anneeScolaire' => $classeEleve->anneeClasse->annee->annee_debut . ' - ' . $classeEleve->anneeClasse->annee->annee_fin,
            'classeEleve_id' => $classeEleve->id,
        ],
        'status' => 200
    ]);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClasseEleve $classeEleve)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClasseEleveRequest $request, ClasseEleve $classeEleve)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClasseEleve $classeEleve)
    {
        //
    }
}
