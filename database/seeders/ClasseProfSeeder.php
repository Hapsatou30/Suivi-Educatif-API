<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use App\Models\AnneeClasse;
use App\Models\ProfMatiere;
use Illuminate\Database\Seeder;

class ClasseProfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les années scolaires ayant au moins un élève inscrit via ClasseEleve
        $anneeClasses = AnneeClasse::with('classeEleves.eleve')->has('classeEleves')->get();
        // Afficher les années de classe pour débogage
        if ($anneeClasses->isEmpty()) {
            echo "Aucune année de classe trouvée avec des élèves.\n";
            return;
        }


        // Récupérer tous les professeurs et matières
        $profMatieres = ProfMatiere::all();

        // Associer chaque matière-professeur à une année-classe
        foreach ($anneeClasses as $anneeClasse) {
            foreach ($profMatieres as $profMatiere) {
                // Créer une association entre l'année-classe et la matière-professeur
                ClasseProf::create([
                    'annee_classe_id' => $anneeClasse->id,
                    'prof_mat_id'     => $profMatiere->id,
                ]);
            }
        }
    }
}
