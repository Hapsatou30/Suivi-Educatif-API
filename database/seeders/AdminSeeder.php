<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Exécute les seeds pour la table 'Admin'.
     */
    public function run(): void
    {
        // Récupérer tous les utilisateurs ayant le rôle 'admin' à partir de la table 'Users'
        $admins = User::role('admin')->get();

        // Parcourir chaque utilisateur ayant le rôle 'admin'
        foreach ($admins as $adminUser) {
            // Créer ou mettre à jour l'enregistrement dans la table 'Admin' pour l'utilisateur actuel
            // La méthode 'updateOrCreate' cherche un enregistrement existant basé sur 'user_id'
            // Si l'enregistrement n'existe pas, il le crée avec 'user_id'
            Admin::updateOrCreate([
                'user_id' => $adminUser->id,
            ]);
        }
    }
}
