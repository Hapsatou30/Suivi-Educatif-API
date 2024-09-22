<?php

namespace Database\Seeders;

use App\Models\Eleve;
use App\Models\Bulletin;
use App\Models\Evaluations;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les évaluations
        $evaluations = Evaluations::all();

        // Récupérer tous les élèves
        $eleves = Eleve::all();

       

        // Boucle pour insérer des notes
        foreach ($evaluations as $evaluation) {
            foreach ($eleves as $eleve) {
                    // Insérer une note pour chaque élève, évaluation et bulletin
                    DB::table('notes')->insert([
                        'notes' => rand(0, 200) / 10, // Note aléatoire entre 0 et 20
                        'commentaire' => 'Commentaire pour l\'élève ' . $eleve->id,
                        'evaluation_id' => $evaluation->id,
                        'eleve_id' => $eleve->id,
                        'bulletin_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        
    }
}
