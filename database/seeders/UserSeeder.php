<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Liste des utilisateurs avec leurs emails, mots de passe spécifiques et les roles
        $users = [
            ['email' => 'thiamhapstou@gmail.com', 'password' => Hash::make('password123'),"role" => "admin"],
            ['email' => 'hapsthiam@gmail.com', 'password' => Hash::make('password123'),"role" => "professeur"],
            ['email' => 'hapsatou.thiam@univ-thies.sn', 'password' => Hash::make('password123'),"role" => "parent"],
            ['email' => 'parent@gmail.com', 'password' => Hash::make('password123'),"role" => "parent"],
            ['email' => 'prof@gmail.com', 'password' => Hash::make('password123'),"role" => "professeur"],
            ['email' => 'eleve@gmail.com', 'password' => Hash::make('password123'),"role" => "eleve"],
            ['email' => 'eleve1@gmail.com', 'password' => Hash::make('password123'),"role" => "eleve"],
            ['email' => 'parent1@gmail.com', 'password' => Hash::make('password123'),"role" => "parent"],
            ['email' => 'prof1@gmail.com', 'password' => Hash::make('password123'),"role" => "professeur"],
            ['email' => 'eleve2@gmail.com', 'password' => Hash::make('password123'),"role" => "eleve"],
        ];

        // Insertion des utilisateurs dans la table 'users'
        foreach ($users as $user) {
            User::create($user);
        }
    
    }
}
