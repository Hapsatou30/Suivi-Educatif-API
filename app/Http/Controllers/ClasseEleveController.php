<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Parents;
use App\Models\AnneeClasse;
use App\Models\ClasseEleve;
use App\Models\AnneeScolaire;
use App\Http\Requests\StoreClasseEleveRequest;
use App\Http\Requests\UpdateClasseEleveRequest;

class ClasseEleveController extends Controller
{

  
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
    public function index()
{
    // Récupérer les années_classes de l'année en cours avec leurs élèves
    $anneeClasses = AnneeClasse::whereHas('annee', function ($query) {
        $query->where('etat', 'En_cours'); // Filtrer par état "En_cours"
    })
    ->with('classe', 'eleves')
    ->get();

    // Structurer la réponse en JSON
    return response()->json([
        'message' => 'Liste des élèves pour l\'année en cours',
        'données' => $anneeClasses->map(function ($anneeClasse) {
            return [
                'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin, // Afficher l'année scolaire
                'classe' => $anneeClasse->classe->nom, 
                'id' => $anneeClasse->classe->id,
                'eleves' => $anneeClasse->eleves->map(function ($eleve) {
                    return [
                        'id' => $eleve->id,  // Afficher l'ID de l'élève
                        'nom' => $eleve->nom,
                        'prenom' => $eleve->prenom,
                        'matricule' => $eleve->matricule,
                        'date_naissance' => $eleve->date_naissance,
                        'telephone' => $eleve->telephone,
                        'parent' => $eleve->parent->prenom. ' ' . $eleve->parent->nom,
                        'parent_telephone' => $eleve->parent->telephone
                    ];
                }),
            ];
        }),
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
            'annee_classe' => $eleve->anneeClasses->map(function ($anneeClasse) {
                return [
                    'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
                    'classe' => $anneeClasse->classe->nom,
                    
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
                $classeEleves[] = ClasseEleve::create([
                    'annee_classe_id' => $validated['annee_classe_id'],
                    'eleve_id' => $eleve_id,
                ]);
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
        //
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
