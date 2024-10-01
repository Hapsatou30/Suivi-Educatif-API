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

        // Pour chaque élève, associer à une année-classe
        $eleves = Eleve::all();

        foreach ($eleves as $eleve) {
            // Vérifier si l'élève est déjà associé à une classe pour une année scolaire
            $exists = DB::table('classe_eleves')
                        ->where('eleve_id', $eleve->id)
                        ->exists();

            // Si l'élève n'est pas encore associé, on lui attribue une année-classe
            if (!$exists) {
                // Sélectionner une année-classe aléatoire
                $anneeClasse = $anneeClasses->random();

                // Insérer l'association dans la table classe_eleves
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
