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
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les professeurs et les matières
        $professeurs = Professeur::all();
        $matieres = Matiere::all();

        // Associer chaque professeur à une matière
        foreach ($professeurs as $professeur) {
            // Sélectionner une matière aléatoirement
            $matiere = $matieres->random();

            ProfMatiere::create([
                'professeur_id' => $professeur->id,
                'matiere_id'    => $matiere->id,
            ]);
        }
    }
}
