<?php

namespace App\Http\Controllers;

use App\Models\ClasseProf;
use App\Models\AnneeClasse;
use App\Models\ProfMatiere;
use App\Models\AnneeScolaire;
use App\Http\Requests\StoreClasseProfRequest;
use App\Http\Requests\UpdateClasseProfRequest;

class ClasseProfController extends Controller
{
    /**
     * Methode pour afficher les classe des professeurs et les matieres enseignés dans ces classes
     */
    public function index()
    {
// Récupérer toutes les années scolaires avec leurs classes via la table pivot annee_classes
$annees = AnneeScolaire::with(['classes.annees', 'classes.anneeClasses.profMatieres.professeur', 'classes.anneeClasses.profMatieres.matiere'])->get();

$data = [];

// Parcourir chaque année
foreach ($annees as $annee) {
    $professeurs = [];

    // Parcourir les classes associées à cette année via la table pivot annee_classe
    foreach ($annee->classes as $classe) {
        // Parcourir les relations annee_classes -> prof_matieres pour chaque classe
        foreach ($classe->anneeClasses as $anneeClasse) {
            foreach ($anneeClasse->profMatieres as $profMatiere) {
                $professeurId = $profMatiere->professeur->id;

                // Vérifier si le professeur a déjà été ajouté
                if (!isset($professeurs[$professeurId])) {
                    $professeurs[$professeurId] = [
                        'nom' => $profMatiere->professeur->nom,
                        'prenom' => $profMatiere->professeur->prenom,
                        'classes' => [],
                    ];
                }

                // Ajouter la classe et la matière enseignée
                $professeurs[$professeurId]['classes'][] = [
                    'nom_classe' => $classe->nom,
                    'matiere' => $profMatiere->matiere->nom,
                ];
            }
        }
    }

    // Ajouter les données de l'année et des professeurs
    $data[] = [
        'annee' => $annee->annee_debut . ' - ' . $annee->annee_fin,
        'professeurs' => $professeurs,
    ];
}

// Retourner les données sous forme de JSON
return response()->json($data);
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClasseProfRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {   
        // Trouver la ClasseProf par son ID
        $classeProf = ClasseProf::with(['profMatiere.professeur', 'profMatiere.matiere', 'anneeClasse.classe'])
                                 ->find($id);
        //Vérifier si classe prof existe
        if (!$classeProf) {
            return response()->json(['error' => 'ClasseProf non trouvée'], 404);
        }
    
        // Préparer le professeur
        $profMatiere = $classeProf->profMatiere;
        if (!$profMatiere) {
            return response()->json(['error' => 'ProfMatiere non trouvée pour cette ClasseProf'], 404);
        }
    
        $professeur = $profMatiere->professeur;
        if (!$professeur) {
            return response()->json(['error' => 'Professeur non trouvé pour cette ProfMatiere'], 404);
        }
    
        // Récupérer toutes les matières et classes du professeur
        $profMatieres = ProfMatiere::with(['matiere', 'anneeClasses.classe'])
                                    ->where('professeur_id', $professeur->id)
                                    ->get();
    
        // Construire la réponse
        $classesEtMatieres = [];
        foreach ($profMatieres as $pm) {
            foreach ($pm->anneeClasses as $anneeClasse) {
                $classesEtMatieres[] = [
                    'nom_classe' => $anneeClasse->classe->nom,
                    'matiere' => $pm->matiere->nom,
                    'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
                ];
            }
        }
    
        $response = [
            'professeur' => [
                'nom' => $professeur->nom,
                'prenom' => $professeur->prenom,
            ],
            'classes_matieres' => $classesEtMatieres,
        ];
    
        return response()->json($response);
    }
    

    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClasseProfRequest $request, ClasseProf $classeProf)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClasseProf $classeProf)
    {
        //
    }
}
