<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use App\Models\ClasseEleve;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
       // Récupérer toutes les associations entre classes et élèves
       $classeEleves = ClasseEleve::all();
       // Récupérer toutes les associations entre classes et professeurs
       $classeProfs = ClasseProf::all();

       // Pour chaque combinaison classe-élève et classe-professeur, créer une présence
       foreach ($classeEleves as $classeEleve) {
           foreach ($classeProfs as $classeProf) {
               // Générer aléatoirement le statut (présent ou absent)
               $status = ['present', 'absent'][rand(0, 1)];

               // Si l'élève est absent, on ajoute un motif et une justification
               $motif = $status === 'absent' ? 'Absence justifiée' : null;
               $justification = $status === 'absent' ? 'Justification fournie' : null;

               // Créer la présence
               DB::table('presences')->insert([
                   'date_presence' => now()->subDays(rand(0, 30)), // Date aléatoire dans le dernier mois
                   'status' => $status,
                   'motif' => $motif,
                   'justification' => $justification,
                   'classe_eleve_id' => $classeEleve->id,
                   'classe_prof_id' => $classeProf->id,
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
           }
       }
    }
}
