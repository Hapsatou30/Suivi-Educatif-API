<?php

namespace Database\Seeders;

use App\Models\Eleve;
use App\Models\Classe;
use App\Models\AnneeClasse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClasseEleveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'année scolaire en cours
        $anneeClasses = AnneeClasse::all();

        // Pour chaque année-classe, associer les élèves
        foreach ($anneeClasses as $anneeClasse) {
            // Récupérer tous les élèves
            $eleves = Eleve::all();

            foreach ($eleves as $eleve) {
                // Créer une association entre l'année-classe et l'élève
                DB::table('classe_eleves')->insert([
                    'annee_classe_id' => $anneeClasse->id,
                    'eleve_id' => $eleve->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
