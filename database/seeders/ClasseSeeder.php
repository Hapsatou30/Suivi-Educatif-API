<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertion des classes
        $classes = [
            ['nom' => '6ème A', 'niveau' => '6ème', 'capacite' => 30],
            ['nom' => '6ème B', 'niveau' => '6ème', 'capacite' => 30],
            ['nom' => '5ème A', 'niveau' => '5ème', 'capacite' => 30],
            ['nom' => '5ème B', 'niveau' => '5ème', 'capacite' => 30],
            ['nom' => '4ème A', 'niveau' => '4ème', 'capacite' => 25],
            ['nom' => '4ème B', 'niveau' => '4ème', 'capacite' => 25],
            ['nom' => '3ème A', 'niveau' => '3ème', 'capacite' => 25],
            ['nom' => '3ème B', 'niveau' => '3ème', 'capacite' => 25],
        ];

        // Créer les enregistrements dans la table 'classes'
        foreach ($classes as $classe) {
            Classe::create($classe);
        }
    }
}
