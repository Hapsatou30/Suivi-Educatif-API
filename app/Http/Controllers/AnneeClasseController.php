<?php

namespace App\Http\Controllers;

use App\Models\AnneeClasse;
use App\Models\AnneeScolaire;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreAnneeClasseRequest;
use App\Http\Requests\UpdateAnneeClasseRequest;

class AnneeClasseController extends Controller
{

    //nombre de classe ouverte pour l'annee en cours
    public function nombreClasseOuverte()
    {
        // Récupérer l'année en cours
        $anneeEnCours = AnneeScolaire::where('etat', 'En_cours')->first();
    
        // Vérifier si une année en cours existe
        if ($anneeEnCours) {
            // Compter le nombre de classes liées à cette année
            $nombreClassesOuvertes = AnneeClasse::where('annee_id', $anneeEnCours->id)
                                                 ->count();
    
            // Retourner le nombre de classes
            return response()->json([
                'message' => 'Nombre de classes ouvertes pour l\'année en cours',
                'nombreClasses' => $nombreClassesOuvertes
            ], 200);
        }
    
        // Si aucune année en cours n'existe
        return response()->json([
            'message' => 'Aucune année en cours trouvée',
            'nombreClasses' => 0
        ], 404);
    }
    
    /**
     *liste les classe par annee
     */
    public function index()
    {
        // Récupérer la liste des années scolaires avec leurs classes associées
        $anneesScolaires = AnneeScolaire::with(['classes' => function($query) {
        }])->where('etat', 'En_cours')->get(); // Filtrer les années scolaires avec l'état 'En_cours'
    
        // Mapper les résultats pour obtenir les informations nécessaires
        $resultats = $anneesScolaires->map(function ($annee) {
            return [
                'id' => $annee->id, // ID de l'année scolaire
                'annee_debut' => $annee->annee_debut,
                'annee_fin' => $annee->annee_fin,
                'etat' => $annee->etat,
                'classes' => $annee->classes->map(function ($classe) {
                    return [
                        'id' => $classe->id, 
                        'nom' => $classe->nom, 
                        'niveau' => $classe->niveau, 
                        'capacite' => $classe->capacite, 
                        'anneeClasse_id' => $classe->pivot->id,
                    ];
                }),
            ];
        });
    
        return response()->json([
            'message' => 'Liste des années scolaires et leurs classes en cours',
            'données' => $resultats,
            'status' => 200
        ]);
    }
    

//listes des niveau des classes dans une année

public function niveauClasses($anneeId)
{
    // Récupérer l'année scolaire par son ID avec les classes associées
    $annee = AnneeScolaire::with('classes')->find($anneeId);

    // Vérifier si l'année existe
    if (!$annee) {
        return response()->json([
            'message' => 'Année scolaire non trouvée.',
            'status' => 404
        ]);
    }
    
    // Récupérer les niveaux distincts des classes
    $niveaux = $annee->classes->pluck('niveau')->unique();

    // Vérifier si des niveaux existent
    if ($niveaux->isEmpty()) {
        return response()->json([
            'message' => 'Aucun niveau trouvé pour cette année scolaire.',
            'status' => 404
        ]);
    }

    return response()->json([
        'message' => 'Liste des niveaux des classes pour l\'année scolaire',
        'données' => $niveaux,
        'status' => 200
    ]);
}


  
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnneeClasseRequest $request)
    {
        // Récupérer les données validées
        $data = $request->validated();
        
        // Vérifier que l'année spécifiée est en cours
        $anneeScolaire = AnneeScolaire::where('id', $data['annee_id'])->first();
    
        if (!$anneeScolaire || $anneeScolaire->etat !== 'En_cours') {
            return response()->json([
                'message' => 'L\'année spécifiée n\'est pas en cours ou n\'existe pas.',
                'status' => 404
            ]);
        }
    
        // Récupérer les classes actuellement associées
        $idClasseActu = $anneeScolaire->classes()->pluck('classes.id')->toArray();
    
        // Déterminer les classes à ajouter et à retirer
        $classeRetirer = array_diff($idClasseActu, $data['classe_ids']);
        $classeAjouter = array_diff($data['classe_ids'], $idClasseActu);
    
        // Supprimer les classes qui ne sont plus sélectionnées
        if (!empty($classeRetirer)) {
            $anneeScolaire->classes()->detach($classeRetirer);
        }
    
        // Ajouter les nouvelles classes (si elles ne sont pas déjà associées)
        if (!empty($classeAjouter)) {
            $anneeScolaire->classes()->syncWithoutDetaching($classeAjouter);
        }
    
        return response()->json([
            'message' => 'Classes synchronisées avec succès pour l\'année scolaire',
            'status' => 200
        ]);
    }
    
    
    

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    // Récupérer l'instance d'AnneeClasse par ID, en incluant la relation classe
    $anneeClasse = AnneeClasse::with('classe')->find($id);

    // Vérifier si l'instance existe
    if (!$anneeClasse) {
        return response()->json([
            'message' => 'Année classe non trouvée',
            'status' => 404
        ]);
    }

    // Vérifier si la classe existe
    $classe = $anneeClasse->classe;

    return response()->json([
        'message' => 'Détails d\'une année classe',
        'id_annee_classe' => $anneeClasse->id,
        'donnees_classe' => $classe ? [
            'nom' => $classe->nom,
            'capacite' => $classe->capacite,
        ] : null, // Si la classe n'existe pas, renvoie null
        'status' => 200
    ]);
}


    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnneeClasseRequest $request, AnneeClasse $anneeClasse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnneeClasse $anneeClasse)
    {
        //
    }
}
