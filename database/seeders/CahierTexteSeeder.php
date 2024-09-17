<?php

namespace Database\Seeders;

use App\Models\ClasseProf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class CahierTexteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Récupérer toutes les associations entre classes et professeurs
        $classeProfs = ClasseProf::all();

         // Insérer plusieurs entrées pour chaque classe et professeur
         foreach ($classeProfs as $classeProf) {
            DB::table('cahier_textes')->insert([
                [
                    'titre' => 'Cours de Mathématiques ',
                    'resume' => 'Introduction aux équations linéaires et applications pratiques.',
                    'date' => now()->subDays(3), 
                    'ressource' => 'https://example.com/ressource-maths-5eme',
                    'classe_prof_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Histoire',
                    'resume' => 'Étude de la Révolution française et ses impacts sociaux.',
                    'date' => now()->subDays(5), 
                    'ressource' => 'https://example.com/ressource-histoire-4eme',
                    'classe_prof_id' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Physique ',
                    'resume' => 'Loi de Newton et expériences en laboratoire.',
                    'date' => now()->subDays(2), 
                    'ressource' => 'https://example.com/ressource-physique-3eme',
                    'classe_prof_id' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Géographie ',
                    'resume' => 'Introduction à la géographie physique et cartes topographiques.',
                    'date' => now()->subDays(7), 
                    'ressource' => 'https://example.com/ressource-geographie-6eme',
                    'classe_prof_id' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Chimie ',
                    'resume' => 'Réactions chimiques et la table périodique des éléments.',
                    'date' => now()->subDays(1), 
                    'ressource' => 'https://example.com/ressource-chimie-2nde',
                    'classe_prof_id' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            
        }
        
    }
}
