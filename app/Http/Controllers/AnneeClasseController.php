<?php

namespace App\Http\Controllers;

use App\Models\AnneeClasse;
use App\Models\AnneeScolaire;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreAnneeClasseRequest;
use App\Http\Requests\UpdateAnneeClasseRequest;

class AnneeClasseController extends Controller
{
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
        // Récupérer les données
        $data = $request->validated();
    
        
        // Vérifier que l'année spécifiée est en cours
        $anneeScolaire = AnneeScolaire::where('id', $data['annee_id'])
        ->first();

        if (!$anneeScolaire || $anneeScolaire->etat !== 'en_cours') {
        return response()->json([
        'message' => 'L\'année spécifiée n\'est pas en cours ou n\'existe pas.',
        'status' => 404
        ]);
        }

    
        // Vérifiez que les IDs des classes sont bien récupérés
        Log::info('IDs des classes:', $data['classe_ids']);
    
        // Récupérer les classes actuellement associées à l'année scolaire
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
