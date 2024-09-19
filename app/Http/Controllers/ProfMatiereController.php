<?php

namespace App\Http\Controllers;

use App\Models\Professeur;
use App\Models\ProfMatiere;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfMatiereRequest;
use App\Http\Requests\UpdateProfMatiereRequest;

class ProfMatiereController extends Controller
{
    /**
     * Methode pour afficher la liste des matieres d'un prof
     */
    public function index()
    {
        // Récupérer la liste des professeurs avec leurs matières associées
        $professeurs = Professeur::with('matieres')->get();
    
        // Mapper les résultats pour obtenir les informations nécessaires
        $resultats = $professeurs->map(function ($professeur) {
            return [
                'nom' => $professeur->nom,
                'prenom' => $professeur->prenom,
                'matieres' => $professeur->matieres->map(function ($matiere) {
                    return [
                        'nom' => $matiere->nom,
                        'description' => $matiere->description,
                        'coefficient' => $matiere->coefficient,
                    ];
                }),
            ];
        });
    
        return response()->json([
            'message' => 'Liste des professeurs et leurs matières',
            'données' => $resultats,
            'status' => 200
        ]);
    }
    
    

    

    /**
     * Methode pour attibuer des matieres à un pro
     * 
     */
    public function store(StoreProfMatiereRequest $request)
    {
        // Récupérer les données
    $data = $request->validated();

    // Récupérer le professeur
    $professeur = Professeur::findOrFail($data['professeur_id']);
    
    // Vérifiez que les IDs des matières sont bien récupérés
    Log::info('IDs des matières:', $data['matiere_ids']);

    // Récupérer les matières actuellement associées
    $idMatiereActu = $professeur->matieres()->pluck('matieres.id')->toArray();
    
    // Déterminer les matières à ajouter et à retirer
    $matiereRetirer = array_diff($idMatiereActu, $data['matiere_ids']);
    $matiereAjouter = array_diff($data['matiere_ids'], $idMatiereActu);

    // Supprimer les matières qui ne sont plus sélectionnées
    if (!empty($matiereRetirer)) {
        $professeur->matieres()->detach($matiereRetirer);
    }

    // Ajouter les nouvelles matières (si elles ne sont pas déjà associées)
    if (!empty($matiereAjouter)) {
        $professeur->matieres()->syncWithoutDetaching($matiereAjouter);
    }

    return response()->json([
        'message' => 'Matières synchronisées avec succès pour le professeur',
        'status' => 200
    ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(ProfMatiere $profMatiere)
    {
        //
    }

   
    /**
     * Methode pour modifier les matieres d'un prof 
     */
    public function update(UpdateProfMatiereRequest $request, ProfMatiere $profMatiere)
    {
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfMatiere $profMatiere)
    {
        //
    }
}
