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
                    'ressource' => 'https://www.google.com/url?sa=t&source=web&rct=j&opi=89978449&url=https://www.maths-et-tiques.fr/index.php/cours-maths&ved=2ahUKEwjboNDVyYWJAxUeYEEAHX3MKb0QFnoECBsQAQ&usg=AOvVaw0CIA5oudnrJ2iHzgyxtP4X',
                    'classe_prof_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Histoire',
                    'resume' => 'Étude de la Révolution française et ses impacts sociaux.',
                    'date' => now()->subDays(5), 
                    'ressource' => 'https://www.google.com/url?sa=t&source=web&rct=j&opi=89978449&url=https://www.maxicours.com/se/histoire/&ved=2ahUKEwjZ_9vjyYWJAxWNa0EAHUgbG9EQFnoECA0QAQ&usg=AOvVaw1l8WUagLlF-1NcX3qh26g0',
                    'classe_prof_id' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Physique ',
                    'resume' => 'Loi de Newton et expériences en laboratoire.',
                    'date' => now()->subDays(2), 
                    'ressource' => 'https://www.google.com/url?sa=t&source=web&rct=j&opi=89978449&url=https://www.maxicours.com/se/physique-chimie/&ved=2ahUKEwiOib7uyYWJAxXgV0EAHdEKAbwQFnoECBsQAQ&usg=AOvVaw0tVeTKAF-ryXcwKgtvmmhV',
                    'classe_prof_id' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Géographie ',
                    'resume' => 'Introduction à la géographie physique et cartes topographiques.',
                    'date' => now()->subDays(7), 
                    'ressource' => 'https://www.google.com/url?sa=t&source=web&rct=j&opi=89978449&url=https://www.maxicours.com/se/geographie/&ved=2ahUKEwjTzsb6yYWJAxVlUUEAHSoVN6IQFnoECBoQAQ&usg=AOvVaw0-687Z6wfEVU8n3-aIG7x2',
                    'classe_prof_id' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titre' => 'Cours de Chimie ',
                    'resume' => 'Réactions chimiques et la table périodique des éléments.',
                    'date' => now()->subDays(1), 
                    'ressource' => 'https://www.google.com/url?sa=t&source=web&rct=j&opi=89978449&url=https://www.lachimie.net/&ved=2ahUKEwjLz8mDyoWJAxWJUkEAHXxpHy8QFnoECBwQAQ&usg=AOvVaw2Ek8bWLTsBE80iqbF6ZD10',
                    'classe_prof_id' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            
        }
        
    }
}
