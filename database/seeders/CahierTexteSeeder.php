<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CahierTexteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les associations entre classes et professeurs
        $classeProfs = ClasseProf::all();

        // Insérer 2 entrées pour chaque classe_prof
        foreach ($classeProfs as $classeProf) {
            DB::table('cahier_textes')->insert([
                [
                    'titre' => 'Cours de ' . $classeProf->profMatiere->matiere->nom,
                    'resume' => 'Introduction au premier sujet de ' . $classeProf->profMatiere->matiere->nom,
                    'date' => now()->subDays(rand(1, 7)), // Date aléatoire dans la dernière semaine
                    'ressource' => 'https://www.exemple.com/cours-' . strtolower($classeProf->profMatiere->matiere->nom),
                    'classe_prof_id' => $classeProf->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours avancé de ' . $classeProf->profMatiere->matiere->nom,
                    'resume' => 'Deuxième partie du sujet de ' . $classeProf->profMatiere->matiere->nom,
                    'date' => now()->subDays(rand(8, 14)), // Date aléatoire dans la semaine précédente
                    'ressource' => 'https://www.exemple.com/cours-avance-' . strtolower($classeProf->profMatiere->matiere->nom),
                    'classe_prof_id' => $classeProf->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
