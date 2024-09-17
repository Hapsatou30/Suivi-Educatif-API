<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Eleve;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EleveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

         // Données fictives pour les élèves
        $elevesData = [
            ['nom' => 'Fall', 'prenom' => 'Amina Zahra', 'date_naissance' => '2005-01-15', 'telephone' => '0601020304', 'genre' => 'Féminin', 'photo' => 'https://img.freepik.com/photos-gratuite/heureux-jeune-ecoliere-porter-sac-dos-tenue-livres_141793-80643.jpg?t=st=1726547314~exp=1726550914~hmac=b1075a25d4a3131154263b4d783f54a023e0e4fe9fd72d8d72bc4e0add0fb527&w=740', 'parent_nom' => 'Ka', 'parent_prenom' => 'Kalsoum'],
            ['nom' => 'Fall', 'prenom' => 'Cheikh', 'date_naissance' => '2006-03-22', 'telephone' => '0605060708', 'genre' => 'Masculin', 'photo' => 'https://img.freepik.com/photos-premium/jeune-garcon-se-tient-dans-couloir-son-sac-dos-pret-pour-aventure-ecole_1254878-24672.jpg', 'parent_nom' => 'Ka', 'parent_prenom' => 'Kalsoum'],
            ['nom' => 'Sy', 'prenom' => 'Sir Hamdel', 'date_naissance' => '2004-07-12', 'telephone' => '0612345678', 'genre' => 'Masculin', 'photo' => 'https://img.freepik.com/photos-premium/lyceenne-determinee-posant-dans-couloir_1254878-81647.jpg?w=360', 'parent_nom' => 'Sy', 'parent_prenom' => 'Cheikh Yérim'],
        ];

        // Insérer les élèves dans la base de données
        foreach ($elevesData as $eleveData) {
            // Récupérer l'utilisateur correspondant au nom et prénom du parent
            $parent = User::where('role', 'parent')
                          ->where('nom', $eleveData['parent_nom'])
                          ->where('prenom', $eleveData['parent_prenom'])
                          ->first();

            if ($parent) {
                // Créer un élève et l'associer au parent trouvé
                Eleve::create([
                    'user_id' => User::where('nom', $eleveData['nom'])
                                     ->where('prenom', $eleveData['prenom'])
                                     ->first()->id,
                    'matricule' => 'E' . strtoupper(substr($eleveData['prenom'], 0, 3)) . rand(100, 999),
                    'nom' => $eleveData['nom'],
                    'prenom' => $eleveData['prenom'],
                    'date_naissance' => $eleveData['date_naissance'],
                    'telephone' => $eleveData['telephone'],
                    'genre' => $eleveData['genre'],
                    'photo' => $eleveData['photo'],
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
