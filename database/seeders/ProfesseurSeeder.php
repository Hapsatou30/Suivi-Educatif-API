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
        // Liste des professeurs avec leurs informations
        $professeursData = [
            ['nom' => 'Sall', 'prenom' => 'Fatima', 'email' => 'hapsthiam@gmail.com', 'matricule' => 'PFA' . rand(100, 999)],
            ['nom' => 'Ndiaye', 'prenom' => 'Moussa', 'email' => 'prof@gmail.com', 'matricule' => 'PMO' . rand(100, 999)],
            ['nom' => 'Tavarez', 'prenom' => 'Rachid', 'email' => 'prof1@gmail.com', 'matricule' => 'PRA' . rand(100, 999)],
        ];

        // Créer les enregistrements dans la table des professeurs
        foreach ($professeursData as $professeurData) {
            // Trouver l'utilisateur correspondant à l'email
            $user = User::where('email', $professeurData['email'])
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'professeur');
                        })
                        ->first();
                        
            // Assurer que l'utilisateur existe avant de créer le professeur
            if ($user) {
                Professeur::updateOrCreate(
                    ['user_id' => $user->id], // Rechercher par user_id
                    [
                        'matricule' => $professeurData['matricule'],
                        'nom' => $professeurData['nom'],
                        'prenom' => $professeurData['prenom'],
                        'telephone' => '221' . rand(700000000, 799999999), 
                    ]
                );
            }
        }
    }
}
