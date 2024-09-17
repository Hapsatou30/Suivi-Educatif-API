<?php

namespace Database\Seeders;

use App\Models\AnneeScolaire;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AnneeScolaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertion des années scolaires
        $annees = [
            ['annee_debut' => '2022', 'annee_fin' => '2023', 'etat' => 'Fermée'],
            ['annee_debut' => '2023', 'annee_fin' => '2024', 'etat' => 'En_cours'],
        ];

        // Créer les enregistrements dans la table 'annee_scolaires'
        foreach ($annees as $annee) {
            AnneeScolaire::create($annee);
        }
    }
}
