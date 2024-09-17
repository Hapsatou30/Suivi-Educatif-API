<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Permissions




       // Vérifier si les rôles existent avant de les créer
       $roles = ['admin', 'professeur', 'parent', 'eleve'];

       foreach ($roles as $roleName) {
           Role::firstOrCreate(['name' => $roleName]);
       }


    }
}
