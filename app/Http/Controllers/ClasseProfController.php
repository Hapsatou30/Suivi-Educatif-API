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

    // Récupérer le nombre d'années de classe pour le professeur
    $nombreClasses = ClasseProf::whereHas('profMatiere', function ($query) use ($professeurId) {
        // Filtrer par le professeur
        $query->where('professeur_id', $professeurId);
    })
    ->with('anneeClasse') 
    ->get()
    ->pluck('anneeClasse.id') // Récupérer les IDs des années de classe
    ->count(); // Compter le nombre d'années de classe

    return response()->json([
        'données' => $nombreClasses,
        'status' => 200
    ]);
}

public function listeClassesParProf($professeurId)
{
    // Vérifier si le professeur existe
    $professeur = Professeur::find($professeurId);

    if (!$professeur) {
        return response()->json([
            'message' => 'Professeur non trouvé.',
            'status' => 404
        ]);
    }

    // Récupérer la liste des classes pour le professeur
    $classeProf = ClasseProf::whereHas('profMatiere', function ($query) use ($professeurId) {
        // Filtrer par le professeur
        $query->where('professeur_id', $professeurId);
    })
    ->with(['anneeClasse.classe', 'profMatiere.matiere']) // Charger les relations nécessaires
    ->get()
    ->map(function ($classeProf) {
        return [
            'classeProf_id' => $classeProf->id,
            'annee_classe_id' => $classeProf->annee_classe_id,
            'nom_classe' => $classeProf->anneeClasse->classe->nom, 
            'capacite' => $classeProf->anneeClasse->classe->capacite, 
            'nom_matiere' => $classeProf->profMatiere->matiere->nom 
        ];
    });

    return response()->json([
        'classes' => $classeProf,
        'status' => 200
    ]);
}






// Méthode pour attribuer des classes au professeur
public function store(StoreClasseProfRequest $request)
{
    // Récupérer les données validées
    $data = $request->validated();

    // Vérifier que l'année classe spécifiée est valide
    $anneeClasse = AnneeClasse::find($data['annee_classe_id']);

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

    // Récupérer les IDs des matières actuellement associées
    $idProfMatActu = $anneeClasse->profMatieres()->pluck('prof_matieres.id')->toArray();

    // Vérifier que les IDs des professeurs et matières sont bien récupérés
    Log::info('IDs des prof_mat:', $data['prof_mat_ids']);

    // Déterminer les professeurs et matières à ajouter et à retirer
    $profMatRetirer = array_diff($idProfMatActu, $data['prof_mat_ids']);
    $profMatAjouter = array_diff($data['prof_mat_ids'], $idProfMatActu);

    // Supprimer les professeurs et matières qui ne sont plus sélectionnés
    if (!empty($profMatRetirer)) {
        $anneeClasse->profMatieres()->detach($profMatRetirer);
    }

    // Ajouter les nouveaux professeurs et matières (s'ils ne sont pas déjà associés)
    if (!empty($profMatAjouter)) {
        $anneeClasse->profMatieres()->syncWithoutDetaching($profMatAjouter);
    }

    return response()->json([
        'success' => 'Professeurs et matières synchronisés avec succès pour l\'année classe.',
        'status' => 200
    ]);
}




    /**
     * Display the specified resource.
     */
    public function showProfMatiereClasse($anneeClasseId)
{
    // Récupérer les informations de l'année de classe avec les professeurs et matières associés
    $anneeClasse = AnneeClasse::with(['profMatieres.professeur', 'profMatieres.matiere', 'profMatieres.classeProfs'])
                               ->find($anneeClasseId);

    // Vérifier si l'année de classe existe
    if (!$anneeClasse) {
        return response()->json(['error' => 'Année de classe non trouvée'], 404);
    }

    // Préparer la réponse avec les professeurs et matières associées
    $classes_matieres = [];
    $id_profMat_unique = []; // Tableau pour suivre les ID déjà ajoutés

    foreach ($anneeClasse->profMatieres as $profMatiere) {
        // Si cet id_profMat n'a pas encore été ajouté
        if (!in_array($profMatiere->id, $id_profMat_unique)) {
            // Ajouter l'ID du profMatiere dans le tableau pour vérifier les doublons
            $id_profMat_unique[] = $profMatiere->id;

            // Ajouter les détails dans la réponse
            $classes_matieres[] = [
                'id_profMat' => $profMatiere->id,
                'nom_professeur' => $profMatiere->professeur->nom,
                'prenom_professeur' => $profMatiere->professeur->prenom,
                'matiere' => $profMatiere->matiere->nom,
                'classeProfs' => $profMatiere->classeProfs->map(function ($classeProf) {
                    return [
                        'id_classeProf' => $classeProf->id,
                    ];
                }),
                'annee' => $anneeClasse->annee->annee_debut . ' - ' . $anneeClasse->annee->annee_fin,
            ];
        }
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
