<?php

namespace Database\Seeders;

use App\Models\ClasseEleve;
use App\Models\Evaluations;
use App\Models\Bulletin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les évaluations passées
        $evaluations = Evaluations::where('date', '<', now())->get();

        // Récupérer tous les classeEleves
        $classeEleves = ClasseEleve::all();

        // Définir la période pour laquelle on ajoute les notes (par exemple, 1er semestre)
        $periode = '1_semestre';

        // Boucle pour insérer des notes
        foreach ($evaluations as $evaluation) {
            foreach ($classeEleves as $classeEleve) {
                // Récupérer le bulletin de l'élève pour la période '1_semestre'
                $bulletin = Bulletin::where('classe_eleve_id', $classeEleve->id)
                                    ->where('periode', $periode)
                                    ->first();

                // Si un bulletin existe, vérifier s'il n'y a pas déjà une note pour cette évaluation
                $existingNote = DB::table('notes')
                    ->where('evaluation_id', $evaluation->id)
                    ->where('bulletin_id', $bulletin->id)
                    ->exists();

                // Si aucune note n'existe déjà pour cette évaluation et cet élève, on en crée une
                if ($bulletin && !$existingNote) {
                    DB::table('notes')->insert([
                        'notes' => rand(0, 200) / 10, // Note aléatoire entre 0 et 20
                        'commentaire' => 'Commentaire pour l\'élève ' . $classeEleve->id,
                        'evaluation_id' => $evaluation->id,
                        'bulletin_id' => $bulletin->id, // Lier la note à ce bulletin
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
