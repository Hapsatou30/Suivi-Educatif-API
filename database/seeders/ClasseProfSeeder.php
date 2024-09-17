<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use App\Models\AnneeClasse;
use App\Models\ProfMatiere;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClasseProfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'année scolaire en cours
        $anneeClasses = AnneeClasse::all();

        // Récupérer tous les professeurs et matières
        $profMatieres = ProfMatiere::all();

        // Associer chaque matière-professeur à une année-classe
        foreach ($anneeClasses as $anneeClasse) {
            // Pour chaque année-classe, associer un professeur à une matière
            foreach ($profMatieres as $profMatiere) {
                // Créer une association entre l'année-classe et la matière-professeur
                ClasseProf::create([
                    'annee_classe_id' => $anneeClasse->id,
                    'prof_mat_id'     => $profMatiere->id,
                ]);
            }
        }
    }
}
