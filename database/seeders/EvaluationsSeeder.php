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
            // Créer 2 évaluations aléatoires pour chaque classe-prof
            for ($i = 0; $i < 2; $i++) {
                do {
                    // Générer une date et une heure aléatoires
                    $date = now()->addDays(rand(1, 30));
                    $heure = now()->addHours(rand(8, 16))->format('H:i:s'); // Heure aléatoire entre 8h et 18h
    
                    // Vérifier s'il existe déjà une évaluation à la même date et heure pour cette classe
                    $exists = DB::table('evaluations')
                        ->where('classe_prof_id', $classeProf->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->where('heure', $heure)
                        ->exists();
    
                } while ($exists); // Si une évaluation existe déjà, générer une nouvelle date/heure
    
                // Insérer l'évaluation si elle n'existe pas encore
                DB::table('evaluations')->insert([
                    'classe_prof_id' => $classeProf->id,
                    'nom' => 'Évaluation ' . ($i + 1) . ' de ' . $classeProf->id,
                    'date' => $date, // Date aléatoire dans les 30 jours suivants
                    'heure' => $heure, // Heure aléatoire
                    'duree' => rand(60, 240), // Durée entre 1h et 4h
                    'type_evaluation' => $types_evaluations[array_rand($types_evaluations)], 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
}
