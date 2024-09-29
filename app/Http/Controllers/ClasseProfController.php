<?php

namespace App\Http\Controllers;

use App\Models\ClasseProf;
use App\Models\Professeur;
use App\Models\AnneeClasse;
use App\Models\ProfMatiere;
use Illuminate\Http\Request;
use App\Models\AnneeScolaire;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreClasseProfRequest;
use App\Http\Requests\UpdateClasseProfRequest;

class ClasseProfController extends Controller
{
   

public function index(Request $request)
{
    // Récupérer l'ID de la classe à partir des paramètres de requête
    $anneeClasseId = $request->query('annee_classe_id');

    // Vérifier si l'ID de l'année de classe est fourni
    if (!$anneeClasseId) {
        return response()->json([
            'success' => false,
            'message' => 'L\'ID de l\'année de classe est requis.'
        ], 400);
    }

    // Récupérer les classe_profs avec leurs relations anneeClasse et profMatiere pour l'année de classe donnée
    $classeProfs = ClasseProf::with(['anneeClasse', 'profMatiere'])
        ->where('annee_classe_id', $anneeClasseId)
        ->get();

    // Retourner la liste sous forme de JSON
    return response()->json([
        'success' => true,
        'data' => $classeProfs
    ], 200);
}


    /**
 * Méthode pour récupérer le nombre de classes dans lesquelles un professeur enseigne
 */
public function nombreClassesParProf($professeurId)
{
    // Vérifier si le professeur existe
    $professeur = Professeur::find($professeurId);

    if (!$professeur) {
        return response()->json([
            'message' => 'Professeur non trouvé.',
            'status' => 404
        ]);
    }

    // Récupérer toutes les classes via les relations 
    $nombreClasses = $professeur->profMatieres()
        ->with('anneeClasses.classe') 
        ->get()
        ->pluck('anneeClasse.classe.id') // Extraire les IDs des classes
        ->unique() // Supprimer les doublons
        ->count(); // Compter le nombre de classes uniques

    return response()->json([
        'message' => 'Nombre de classes pour le professeur',
        'données' => $nombreClasses,
        'status' => 200
    ]);
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
    public function showProfMatiereClasse($anneeClasseId)
    {
        // Récupérer les informations de l'année de classe avec les professeurs et matières associés
        $anneeClasse = AnneeClasse::with(['profMatieres.professeur', 'profMatieres.matiere'])
                                   ->find($anneeClasseId);
    
        // Vérifier si l'année de classe existe
        if (!$anneeClasse) {
            return response()->json(['error' => 'Année de classe non trouvée'], 404);
        }
    
        // Préparer la réponse avec les professeurs et matières associées
        $classes_matieres = [];
        foreach ($anneeClasse->profMatieres as $profMatiere) {
            $classes_matieres[] = [
                'nom_professeur' => $profMatiere->professeur->nom,
                'prenom_professeur' => $profMatiere->professeur->prenom,
                'matiere' => $profMatiere->matiere->nom,
                'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
            ];
        }
    
        // Construire la réponse finale
        $response = [
            'annee_classe' => [
                'nom_classe' => $anneeClasse->classe->nom,
                'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
            ],
            'classes_matieres' => $classes_matieres,
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
