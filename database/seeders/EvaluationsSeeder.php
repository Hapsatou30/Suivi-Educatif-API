<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\ClasseProf;
use Illuminate\Database\Seeder;

class EvaluationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Types d'évaluations : Devoir et Examen
        $types_evaluations = ['Devoir', 'Examen'];
    
        // Récupérer toutes les associations classe-professeur
        $classeProfs = ClasseProf::all();
    
        // Insérer des évaluations pour chaque association classe-professeur
        foreach ($classeProfs as $classeProf) {
            // Insérer 2 devoirs avec des dates passées
            for ($i = 1; $i <= 2; $i++) {
                $this->insertEvaluation($classeProf->id, 'Devoir ' . $i, true); // true pour dates passées
            }

            // Insérer 1 examen avec une date future
            $this->insertEvaluation($classeProf->id, 'Examen', false); // false pour date future
        }
    }

    /**
     * Insère une évaluation unique pour une classe-prof donnée
     * @param bool $past Si vrai, génère une date passée ; sinon, une date future.
     */
    private function insertEvaluation(int $classeProfId, string $nomEvaluation, bool $past): void
    {
        do {
            // Générer une date et une heure aléatoires
            if ($past) {
                // Date passée (entre 1 et 30 jours dans le passé)
                $date = now()->subDays(rand(1, 30));
            } else {
                // Date future (entre 1 et 30 jours dans le futur)
                $date = now()->addDays(rand(1, 30));
            }

            $heure = now()->addHours(rand(8, 16))->format('H:i'); // Heure aléatoire entre 8h et 16h

            // Vérifier s'il existe déjà une évaluation à la même date et heure pour cette classe
            $exists = DB::table('evaluations')
                ->where('classe_prof_id', $classeProfId)
                ->where('date', $date->format('Y-m-d'))
                ->where('heure', $heure)
                ->exists();

        } while ($exists); // Si une évaluation existe déjà, générer une nouvelle date/heure

        // Insérer l'évaluation
        DB::table('evaluations')->insert([
            'classe_prof_id' => $classeProfId,
            'nom' => $nomEvaluation,
            'date' => $date->format('Y-m-d'), // Date formatée
            'heure' => $heure, // Heure aléatoire
            'duree' => rand(60, 240), // Durée entre 1h et 4h
            'type_evaluation' => (strpos($nomEvaluation, 'Devoir') !== false) ? 'Devoir' : 'Examen',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
