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
            // Essayer de sélectionner un créneau horaire aléatoirement
            do {
                $horaire = $heures[array_rand($heures)];

                // Vérifier si cet horaire existe déjà pour la même classe_prof le même jour
                $exists = DB::table('horaires')
                    ->where('classe_prof_id', $classeProf->id)
                    ->where('jour', $jour)
                    ->where('heure_debut', $horaire[0])
                    ->exists();

            } while ($exists); // Tant que l'horaire existe, on essaie d'en trouver un autre

            // Insérer l'horaire s'il n'existe pas encore
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
