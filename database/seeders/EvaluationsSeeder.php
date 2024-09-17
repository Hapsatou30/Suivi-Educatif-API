<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\ClasseProf;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EvaluationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Types d'évaluations
        $types_evaluations = ['Devoir', 'Examen'];

        // Récupérer toutes les associations classe-professeur
        $classeProfs = ClasseProf::all();

        // Insérer des évaluations pour chaque association classe-professeur
        foreach ($classeProfs as $classeProf) {
            // Créer 3 évaluations aléatoires pour chaque classe-prof
            for ($i = 0; $i < 3; $i++) {
                DB::table('evaluations')->insert([
                    'classe_prof_id' => $classeProf->id,
                    'nom' => 'Évaluation ' . ($i + 1) . ' de ' . $classeProf->id,
                    'date' => now()->addDays(rand(1, 30)), // Date dans les 30 jours suivants
                    'heure' => now()->addHours(rand(8, 18))->format('H:i:s'), // Heure aléatoire entre 8h et 15h
                    'duree' => rand(60, 240), // Durée entre 1h et 4h
                    'type_evaluation' => $types_evaluations[array_rand($types_evaluations)], 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
