<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs ayant le rôle 'admin'
        $admins = User::where('role', 'admin')->get();

        // Associer chaque utilisateur 'admin' au modèle Admin
        foreach ($admins as $adminUser) {
            Admin::create([
                'user_id' => $adminUser->id,
            ]);
        }
    }
}
