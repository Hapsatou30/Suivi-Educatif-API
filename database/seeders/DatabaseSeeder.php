<?php

namespace Database\Seeders;

use App\Models\ClasseEleve;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            ProfesseurSeeder::class,
            ParentsSeeder::class,
            EleveSeeder::class,
            AnneeScolaireSeeder::class,
            ClasseSeeder::class,
            AnneeClasseSeeder::class,
            ProfMatiereSeeder::class,
            ClasseProfSeeder::class,
            ClasseEleveSeeder::class,
            MatiereSeeder::class,
            NoteSeeder::class,
            BulletinSeeder::class,
            CahierTexteSeeder::class,
            EvaluationsSeeder::class,
            HoraireSeeder::class,
            PresenceSeeder::class,


          


        ]);
    }
}
