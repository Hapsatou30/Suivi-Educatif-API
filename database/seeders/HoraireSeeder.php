<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HoraireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Liste des jours de la semaine
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        // Liste des plages horaires possibles
        $heures = [
            ['08:00:00', '10:00:00'],
            ['10:00:00', '12:00:00'],
            ['12:00:00', '14:00:00'],
            ['14:00:00', '16:00:00'],
            ['16:00:00', '18:00:00'],
           
        ];

        // Récupérer toutes les associations classe-professeur
        $classeProfs = ClasseProf::all();

        // Insérer des horaires pour chaque classe et professeur
        foreach ($classeProfs as $classeProf) {
            // Assigner des horaires aléatoires pour plusieurs jours
            foreach ($jours as $jour) {
                // Sélectionner un créneau horaire aléatoirement
                $horaire = $heures[array_rand($heures)];

                DB::table('horaires')->insert([
                    'jour' => $jour,
                    'heure_debut' => $horaire[0],
                    'heure_fin' => $horaire[1],
                    'classe_prof_id' => $classeProf->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
