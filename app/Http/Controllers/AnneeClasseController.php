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
    $anneesScolaires = AnneeScolaire::with('classes')->get();

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
                ];
            }),
        ];
    });

    return response()->json([
        'message' => 'Liste des années scolaires et leurs classes',
        'données' => $resultats,
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
        $anneeScolaire = AnneeScolaire::where('id', $data['annee_id'])
            ->first();
    
        if (!$anneeScolaire || $anneeScolaire->etat !== 'En_cours') {
            return response()->json([
                'message' => 'L\'année spécifiée n\'est pas en cours ou n\'existe pas.',
                'status' => 404
            ]);
        }
    
        // Ajouter les nouvelles classes sans retirer les anciennes
        if (!empty($data['classe_ids'])) {
            $anneeScolaire->classes()->syncWithoutDetaching($data['classe_ids']);
        }
    
        return response()->json([
            'message' => 'Classes ajoutées avec succès pour l\'année scolaire',
            'status' => 200
        ]);
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(AnneeClasse $anneeClasse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AnneeClasse $anneeClasse)
    {
        //
    }

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
