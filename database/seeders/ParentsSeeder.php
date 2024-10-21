<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Parents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
         // Données fictives pour les parents
        $parentsData = [
            ['nom' => 'Ka', 'prenom' => 'Kalsoum', 'email' => 'parent@gmail.com', 'telephone' => '0601010101', 'adresse' => '10 rue de Marsat'],
            ['nom' => 'Sy', 'prenom' => 'Cheikh Yérim', 'email' => 'parent1@gmail.com', 'telephone' => '0602020202', 'adresse' => '20 avenue de Mermoz'],
        ];

        // Insérer les parents dans la base de données
        foreach ($parentsData as $parentData) {
            // Trouver l'utilisateur correspondant à l'email
            $user = User::where('email', $parentData['email'])
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'parent');
                        })
                        ->first();

            // Assurer que l'utilisateur existe avant de créer le parent
            if ($user) {
                Parents::updateOrCreate(
                    ['user_id' => $user->id], // Rechercher par user_id
                    [
                        'nom'       => $parentData['nom'],
                        'prenom'    => $parentData['prenom'],
                        'telephone' => $parentData['telephone'],
                        'adresse'   => $parentData['adresse'],
                    ]
                );
            }
        }
    }
}
