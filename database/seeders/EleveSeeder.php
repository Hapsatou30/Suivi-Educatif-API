<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Eleve;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class EleveSeeder extends Seeder
{
    /**
     * Exécute les seeds pour la table 'eleves'.
     */
    public function run(): void
    {
        // Liste des élèves avec leurs informations détaillées
        $eleves = [
            ['user_email' => 'eleve@gmail.com', 'matricule' => 'EAM' . rand(100, 999), 'nom' => 'Fall', 'prenom' => 'Amina Zahra', 'date_naissance' => '2006-05-15', 'telephone' => '123456789', 'genre' => 'Feminin', 'parent_id' => 1],
            ['user_email' => 'eleve1@gmail.com', 'matricule' => 'ECH' . rand(100, 999), 'nom' => 'Fall', 'prenom' => 'Cherif', 'date_naissance' => '2007-08-20', 'telephone' => '987654321', 'genre' => 'Masculin', 'parent_id' => 1],
            ['user_email' => 'eleve2@gmail.com', 'matricule' => 'ESI' . rand(100, 999), 'nom' => 'Sy', 'prenom' => 'Sir Hamdel', 'date_naissance' => '2008-12-30', 'telephone' => '456789123', 'genre' => 'Masculin', 'parent_id' => 2],
        ];

        // Parcourir chaque élève de la liste
        foreach ($eleves as $eleve) {
            // Récupérer l'utilisateur correspondant à l'email donné dans la liste
            $user = User::where('email', $eleve['user_email'])->first();

            // Vérifier si l'utilisateur existe
            if ($user) {
                // Créer une entrée dans la table 'eleves' pour l'élève
                Eleve::create([
                    'matricule' => $eleve['matricule'],
                    'nom' => $eleve['nom'],
                    'prenom' => $eleve['prenom'],
                    'date_naissance' => $eleve['date_naissance'],
                    'telephone' => $eleve['telephone'],
                    'genre' => $eleve['genre'],
                    'user_id' => $user->id, // Associer l'élève à l'utilisateur correspondant
                    'parent_id' => $eleve['parent_id'], // Associer l'élève à son parent
                ]);
            } 
        }
    }
}
