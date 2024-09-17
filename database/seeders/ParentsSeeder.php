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
        // // Récupérer les utilisateurs ayant le rôle 'parent'
        // $parents = User::where('role', 'parent')->get();

        // // Associer chaque utilisateur 'parent' au modèle Parents
        // foreach ($parents as $parentUser) {
        //     Parents::create([
        //         'user_id' => $parentUser->id,
        //     ]);
        // }

                // Données fictives pour les parents
                $parentsData = [
                    ['nom' => 'Ka', 'prenom' => 'Kalsoum', 'telephone' => '0601010101', 'adresse' => '10 rue de Marsat', 'photo' => 'https://img.freepik.com/photos-gratuite/femme-smiley-coup-moyen-au-bureau_23-2148924767.jpg?t=st=1726553087~exp=1726556687~hmac=ed663d840baebc2e2cc5f50e58443eb1935dae33e0d93d0663d6fba5dbae1da6&w=740'],
                    ['nom' => 'Sy', 'prenom' => 'Cheikh Yérim', 'telephone' => '0602020202', 'adresse' => '20 avenue de Mermoz', 'photo' => 'https://img.freepik.com/photos-gratuite/gros-plan-du-beau-jeune-entrepreneur-peau-foncee-lunettes-vetements-ceremonie_273609-9177.jpg?t=st=1726553123~exp=1726556723~hmac=1cf895ac0e1ab67426c081efb7ca740653afc0272bf802332e68fa3171454061&w=740'],
                    
                ];
        
                // Insérer les parents dans la base de données
                foreach ($parentsData as $parentData) {
                    // Récupérer l'utilisateur correspondant au nom du parent
                    $user = User::where('role', 'parent')
                                ->where('nom', $parentData['nom'])
                                ->where('prenom', $parentData['prenom']) 
                                ->first();
        
                    if ($user) {
                        Parents::updateOrCreate(
                            ['user_id' => $user->id], // Vérifie si un enregistrement avec cet ID utilisateur existe déjà
                            [
                                'nom'       => $parentData['nom'],
                                'prenom'    => $parentData['prenom'],
                                'telephone' => $parentData['telephone'],
                                'adresse'   => $parentData['adresse'],
                                'photo' => $parentData['photo'],
                            ]
                        );
                    }
                }
    }
}
