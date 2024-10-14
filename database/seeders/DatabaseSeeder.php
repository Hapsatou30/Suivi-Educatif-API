<?php

namespace Database\Seeders;

use App\Models\ClasseEleve;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Cette méthode initialise le processus de "seed" de la base de données de l'application.
     * Elle permet d'exécuter tous les seeders spécifiés pour peupler les tables de la base de données avec des données initiales.
     */
    public function run(): void
    {
        // Appel de plusieurs seeders à l'aide de la méthode "call", chacun correspond à une table ou à un ensemble de tables à remplir
        $this->call([
            UserSeeder::class,               // Seeder pour remplir la table des utilisateurs (Users)
            AdminSeeder::class,              // Seeder pour les administrateurs
            ProfesseurSeeder::class,         // Seeder pour les professeurs
            MatiereSeeder::class,            // Seeder pour les matières (matières enseignées)
            ProfMatiereSeeder::class,        // Seeder pour lier les professeurs et les matières qu'ils enseignent
            ParentsSeeder::class,            // Seeder pour les parents d'élèves
            EleveSeeder::class,              // Seeder pour les élèves
            AnneeScolaireSeeder::class,      // Seeder pour les années scolaires
            ClasseSeeder::class,             // Seeder pour les classes
            AnneeClasseSeeder::class,        // Seeder pour lier les classes et les années scolaires
            ClasseProfSeeder::class,         // Seeder pour lier les professeurs aux classes qu'ils gèrent
            ClasseEleveSeeder::class,        // Seeder pour lier les élèves aux classes
            EvaluationsSeeder::class,        // Seeder pour les évaluations (devoirs, examens.)
            CahierTexteSeeder::class,        // Seeder pour les cahiers de textes (programme scolaire)
          
           
        ]);
    }
}
