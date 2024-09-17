<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\AnneeClasse;
use App\Models\AnneeScolaire;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AnneeClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Récupérer l'année scolaire en cours
        $anneeScolaire = AnneeScolaire::where('etat', 'En_cours')->first();
        
        // Récupérer toutes les classes
        $classes = Classe::all();
 
         // Associer chaque classe à l'année scolaire en cours
         foreach ($classes as $classe) {
             AnneeClasse::create([
                 'annee_id' => $anneeScolaire->id,
                 'classe_id' => $classe->id,
             ]);
         }
         
    }
}
