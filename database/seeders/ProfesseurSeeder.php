<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Professeur;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfesseurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Liste des professeurs à créer avec les IDs utilisateurs spécifiques
        $professeurs = [
            [
                'user_id'   => 2, // ID de l'utilisateur ayant le rôle 'professeur'
                'matricule' => 'PFA' . rand(100, 999),
                'nom'       => 'Sall',
                'prenom'    => 'Fatima',
                'telephone' => '221' . rand(700000000, 799999999),
            ],
            [
                'user_id'   => 5, // ID de l'utilisateur ayant le rôle 'professeur'
                'matricule' => 'PMO' . rand(100, 999),
                'nom'       => 'Ndiaye',
                'prenom'    => 'Moussa',
                'telephone' => '221' . rand(700000000, 799999999),
            ],
            [
                'user_id'   => 9, // ID de l'utilisateur ayant le rôle 'professeur'
                'matricule' => 'PRA' . rand(100, 999),
                'nom'       => 'Tavarez',
                'prenom'    => 'Rachid',
                'telephone' => '221' . rand(700000000, 799999999),
            ],
        ];

        // Créer chaque professeur dans la base de données
        foreach ($professeurs as $professeur) {
            Professeur::updateOrCreate(
                ['user_id' => $professeur['user_id']], // Vérifie si un enregistrement avec cet ID utilisateur existe déjà
                $professeur
            );
        }
    }
}
