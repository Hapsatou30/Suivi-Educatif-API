<?php

namespace App\Http\Controllers;

use App\Models\ClasseProf;
use App\Models\AnneeClasse;
use App\Models\ProfMatiere;
use App\Models\AnneeScolaire;
use Illuminate\Support\Facades\Log;
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

   
 //methode pour attriber des classes au professeur
    public function store(StoreClasseProfRequest $request)
{
    // Récupérer les données validées
    $data = $request->validated();

    // Vérifier que l'année classe spécifiée est valide
    $anneeClasse = AnneeClasse::where('id', $data['annee_classe_id'])->first();

    if (!$anneeClasse) {
        return response()->json([
            'message' => 'L\'année classe spécifiée n\'existe pas.',
            'status' => 404
        ]);
    }

    // Vérifier si l'année scolaire associée à l'année classe est en cours
    $anneeScolaire = $anneeClasse->annee;

    if ($anneeScolaire->etat !== 'En_cours') {
        return response()->json([
            'message' => 'Impossible d\'ajouter des professeurs ou des matières car l\'année scolaire n\'est pas en cours.',
            'status' => 400
        ]);
    }

    // Vérifier que les IDs des prof_matieres sont bien récupérés
    Log::info('IDs des prof_matieres:', $data['prof_mat_ids']);

    // Récupérer les prof_matieres actuellement associés à l'année classe
    $idProfMatActu = $anneeClasse->profMatieres()->pluck('prof_matieres.id')->toArray();

    // Déterminer les prof_matieres à ajouter et à retirer
    $profMatRetirer = array_diff($idProfMatActu, $data['prof_mat_ids']);
    $profMatAjouter = array_diff($data['prof_mat_ids'], $idProfMatActu);

    // Supprimer les prof_matieres qui ne sont plus sélectionnés
    if (!empty($profMatRetirer)) {
        $anneeClasse->profMatieres()->detach($profMatRetirer);
    }

    // Ajouter les nouvelles prof_matieres (si elles ne sont pas déjà associées)
    if (!empty($profMatAjouter)) {
        foreach ($profMatAjouter as $profMatId) {
            ClasseProf::create([
                'annee_classe_id' => $data['annee_classe_id'],
                'prof_mat_id' => $profMatId,
            ]);
        }
    }

    return response()->json([
        'message' => 'Professeurs et matières associés avec succès à l\'année classe.',
        'status' => 200
    ]);
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
