<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MatiereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Insertion des matières
         $matieres = [
            ['nom' => 'Mathématiques', 'description' => 'Étude des nombres et des formes', 'coefficient' => 5],
            ['nom' => 'Physique', 'description' => 'Science de la nature', 'coefficient' => 4],
            ['nom' => 'Histoire', 'description' => 'Étude des faits historiques', 'coefficient' => 3],
            ['nom' => 'Géographie', 'description' => 'Étude des territoires', 'coefficient' => 2],
            ['nom' => 'Chimie', 'description' => 'Science des éléments et des composés', 'coefficient' => 4],
            ['nom' => 'Biologie', 'description' => 'Étude des organismes vivants', 'coefficient' => 4],
            ['nom' => 'Anglais', 'description' => 'Langue étrangère', 'coefficient' => 3],
            ['nom' => 'Français', 'description' => 'Langue et littérature française', 'coefficient' => 3],
            ['nom' => 'Philosophie', 'description' => 'Étude des grandes questions de l’existence', 'coefficient' => 2],
            ['nom' => 'Informatique', 'description' => 'Science des systèmes informatiques', 'coefficient' => 4],
            ['nom' => 'Musique', 'description' => 'Étude des formes musicales', 'coefficient' => 2],
            ['nom' => 'Éducation Physique', 'description' => 'Activités physiques et sportives', 'coefficient' => 3],
        ];
        

        // Créer les enregistrements dans la table 'matieres'
        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
}
