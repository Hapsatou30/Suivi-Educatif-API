<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClasseEleve;
use App\Models\Bulletin;

class BulletinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les élèves avec leurs classes
        $classeEleves = ClasseEleve::all();
        
        // Définir les périodes pour lesquelles nous allons créer les bulletins
        $periodes = ['1_semestre', '2_semestre'];

        // Boucler sur chaque élève dans chaque classe
        foreach ($classeEleves as $classeEleve) {
            foreach ($periodes as $periode) {
                // Vérifier si un bulletin existe déjà pour cette élève et cette période
                $bulletinExiste = Bulletin::where('classe_eleve_id', $classeEleve->id)
                                          ->where('periode', $periode)
                                          ->exists();

                // Si le bulletin n'existe pas, on le crée
                if (!$bulletinExiste) {
                    Bulletin::create([
                        'classe_eleve_id' => $classeEleve->id,
                        'periode' => $periode,
                        'moyenne' => 0,  // Valeur par défaut à mettre à jour plus tard
                        'commentaire' => 'Commentaire par défaut pour ' . $periode,  // Commentaire temporaire
                    ]);
                }
            }
        }

    }
}
