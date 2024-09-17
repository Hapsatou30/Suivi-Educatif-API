<?php

namespace Database\Seeders;

use App\Models\Matiere;
use App\Models\Professeur;
use App\Models\ProfMatiere;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfMatiereSeeder extends Seeder
{
    /**
     * Exécute les seeds pour lier les professeurs aux matières qu'ils enseignent.
     */
    public function run(): void
    {
        // Récupérer tous les professeurs
        $professeurs = Professeur::all();
        
        // Récupérer toutes les matières disponibles
        $matieres = Matiere::all();

        // Parcourir chaque professeur
        foreach ($professeurs as $professeur) {
            // Choisir un nombre aléatoire de matières qu'un professeur peut enseigner (par exemple entre 1 et 3)
            $nombreMatieres = rand(1, 3);

            // Sélectionner aléatoirement un ensemble de matières pour ce professeur
            $matieresEnseignees = $matieres->random($nombreMatieres);

            // Associer le professeur aux matières sélectionnées
            foreach ($matieresEnseignees as $matiere) {
                ProfMatiere::create([
                    'professeur_id' => $professeur->id,
                    'matiere_id'    => $matiere->id,
                ]);
            }
        }
    }
}
