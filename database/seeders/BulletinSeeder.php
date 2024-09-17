<?php

namespace Database\Seeders;

use App\Models\ClasseEleve;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BulletinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Les périodes des bulletins
       $periodes = ['1_semestre', '2_semestre'];

       // Récupérer toutes les associations classe-élève
       $classeEleves = ClasseEleve::all();

       // Insérer des bulletins pour chaque élève dans chaque classe
       foreach ($classeEleves as $classeEleve) {
           foreach ($periodes as $periode) {
               DB::table('bulletins')->insert([
                   'periode' => $periode,
                   'moyenne' => rand(0, 20) + rand(0, 99) / 100,
                   'commentaire' => 'Commentaire du professeur pour ' . $classeEleve->id . ' au ' . $periode,
                   'classe_eleve_id' => $classeEleve->id,
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
           }
       }
    }
}
