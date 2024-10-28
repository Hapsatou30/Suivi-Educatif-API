<?php

namespace App\Http\Controllers;

use App\Models\ClasseProf;
use App\Models\Professeur;
use App\Models\AnneeClasse;
use App\Models\ProfMatiere;
use Illuminate\Http\Request;
use App\Models\AnneeScolaire;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreClasseProfRequest;
use App\Http\Requests\UpdateClasseProfRequest;

class ClasseProfController extends Controller
{
    use NotificationTrait; 

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

    // Récupérer l'année scolaire en cours (où 'etat' = 'En_cours')
    $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

    if (!$anneeScolaireEnCours) {
        return response()->json([
            'message' => 'Aucune année scolaire en cours.',
            'status' => 404
        ]);
    }

    // Récupérer le nombre de classes pour le professeur pour l'année scolaire en cours
    $nombreClasses = ClasseProf::whereHas('profMatiere', function ($query) use ($professeurId) {
        // Filtrer par le professeur
        $query->where('professeur_id', $professeurId);
    })
    ->whereHas('anneeClasse', function ($query) use ($anneeScolaireEnCours) {
        // Filtrer par l'année scolaire en cours
        $query->where('annee_id', $anneeScolaireEnCours->id);
    })
    ->count(); // Compter le nombre de classes

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

    // Récupérer l'année scolaire en cours (où 'etat' = 'En_cours')
    $anneeScolaireEnCours = AnneeScolaire::where('etat', 'En_cours')->first();

    if (!$anneeScolaireEnCours) {
        return response()->json([
            'message' => 'Aucune année scolaire en cours.',
            'status' => 404
        ]);
    }

    // Récupérer la liste des classes pour le professeur pour l'année scolaire en cours
    $classeProf = ClasseProf::whereHas('profMatiere', function ($query) use ($professeurId) {
        // Filtrer par le professeur
        $query->where('professeur_id', $professeurId);
    })
    ->whereHas('anneeClasse', function ($query) use ($anneeScolaireEnCours) {
        // Filtrer par l'année scolaire en cours
        $query->where('annee_id', $anneeScolaireEnCours->id);
    })
    ->with(['anneeClasse.classe', 'profMatiere.matiere']) // Charger les relations nécessaires
    ->get()
    ->map(function ($classeProf) {
        return [
            'classeProf_id' => $classeProf->id,
            'annee_classe_id' => $classeProf->anneeClasse->id, // Assurez-vous d'accéder à la bonne propriété
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
    
      // Notifications
      foreach ($profMatAjouter as $profMatId) {
        $profMatiere = ProfMatiere::find($profMatId); // Récupérer l'association Prof/Matière
        $professeur = $profMatiere->professeur; // Le professeur
        $matiere = $profMatiere->matiere; // La matière
        $classe = $anneeClasse->classe; // La classe

        // Notification pour le professeur
        $this->sendNotification($professeur->user, "Vous avez été ajouté à une nouvelle classe : {$classe->nom}, pour la matière : {$matiere->nom}.");

        // Notification pour le parent de chaque élève
        foreach ($anneeClasse->eleves as $eleve) {
            $parent = $eleve->parent->user;
            $this->sendNotification($parent, "Votre enfant, {$eleve->prenom}, a un nouveau professeur Mme/Mr : {$professeur->nom} pour la matière {$matiere->nom}.");

            // Notification pour l'élève
            $eleveUser = $eleve->user;
            $this->sendNotification($eleveUser, "Vous avez un nouveau professeur Mme/Mr : {$professeur->nom} pour la matière {$matiere->nom}.");
        }
    }
}
    return response()->json([
        'success' => 'Professeurs et matières synchronisés avec succès pour l\'année classe.',
        'status' => 200
    ]);
}

public function listeClasseProfsParAnneeClasse($anneeClasseId)
{
    // Vérifier si l'annéeClasse existe
    $anneeClasse = AnneeClasse::find($anneeClasseId);

    if (!$anneeClasse) {
        return response()->json([
            'message' => 'Année classe non trouvée.',
            'status' => 404
        ]);
    }

    // Récupérer les ClasseProfs pour l'année de classe donnée avec les relations nécessaires
    $classeProfs = ClasseProf::where('annee_classe_id', $anneeClasseId)
        ->with(['profMatiere.professeur', 'profMatiere.matiere'])
        ->get()
        ->map(function ($classeProf) {
            return [
                'id_classeProf' => $classeProf->id, 
                'profMat_id' => $classeProf->profMatiere->id,
                'prenom_prof' => $classeProf->profMatiere->professeur->prenom, // Prénom du professeur
                'nom_prof' => $classeProf->profMatiere->professeur->nom, // Nom du professeur
                'matiere' => $classeProf->profMatiere->matiere->nom ,
                'coefficient' => $classeProf->profMatiere->matiere->coefficient
            ];
        });

    // Retourner la réponse sous forme de JSON
    return response()->json([
        'success' => true,
        'data' => $classeProfs
    ], 200);
}



    /**
     * Display the specified resource.
     */
   
    

    
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
