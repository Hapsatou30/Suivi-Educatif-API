<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use App\Models\ClasseEleve;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les associations entre classes et élèves
        $classeEleves = ClasseEleve::all();

        // Pour chaque élève dans une classe, générer éventuellement une absence
        foreach ($classeEleves as $classeEleve) {
            // Tirer au sort si cet élève aura une absence (0 ou 1)
            $hasAbsence = rand(0, 1); // 0 = pas d'absence, 1 = une absence

            if ($hasAbsence) {
                // Récupérer aléatoirement une association anneeClasse-professeur pour cet élève
                $classeProf = ClasseProf::where('annee_classe_id', $classeEleve->annee_classe_id)->inRandomOrder()->first();

                if ($classeProf) {
                    // Générer les données de l'absence
                    $dateAbsence = now()->subDays(rand(0, 30)); // Date aléatoire dans le dernier mois
                    $status = 'absent'; // Si une absence est générée, l'élève est absent

                    // Insérer l'absence dans la base de données
                    DB::table('absences')->insert([
                        'date_absence' => $dateAbsence,
                        'status' => $status,
                        'classe_eleve_id' => $classeEleve->id,
                        'classe_prof_id' => $classeProf->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
