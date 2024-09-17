<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Exécute les seeds pour la table 'User'.
     */
    public function run(): void
    {
        // Liste des utilisateurs à créer avec leurs emails, mots de passe et rôles associés
        $users = [
            ['email' => 'thiamhapstou@gmail.com', 'password' => Hash::make('password123'), 'role' => 'admin'],
            ['email' => 'hapsthiam@gmail.com', 'password' => Hash::make('password123'), 'role' => 'professeur'],
            ['email' => 'hapsatou.thiam@univ-thies.sn', 'password' => Hash::make('password123'), 'role' => 'parent'],
            ['email' => 'parent@gmail.com', 'password' => Hash::make('password123'), 'role' => 'parent'],
            ['email' => 'prof@gmail.com', 'password' => Hash::make('password123'), 'role' => 'professeur'],
            ['email' => 'eleve@gmail.com', 'password' => Hash::make('password123'), 'role' => 'eleve'],
            ['email' => 'eleve1@gmail.com', 'password' => Hash::make('password123'), 'role' => 'eleve'],
            ['email' => 'prof1@gmail.com', 'password' => Hash::make('password123'), 'role' => 'professeur'],
            ['email' => 'eleve2@gmail.com', 'password' => Hash::make('password123'), 'role' => 'eleve'],
        ];

        // Boucle à travers chaque utilisateur dans la liste
        foreach ($users as $user) {
            // Création de l'utilisateur avec l'email et le mot de passe spécifiés
            $userModel = User::create([
                'email' => $user['email'],
                'password' => $user['password'],
            ]);

            // Récupérer le rôle correspondant au nom spécifié dans la base de données
            $role = Role::where('name', $user['role'])->first();

            // Si le rôle existe, on l'assigne à l'utilisateur créé
            if ($role) {
                $userModel->assignRole($role);
            }
        }
    }
}
