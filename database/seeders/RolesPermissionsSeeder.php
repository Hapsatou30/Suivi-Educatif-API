<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Définir le guard (exemple : 'api' ou 'web')
        $guard = 'api'; 

        // Permissions
        $permissions = [
            'Créer une matiere',
            'Modifier une matiere',
            'Supprimer une matiere',
            'Voir liste des matieres',
            'Voir details matiere',
            'Voir le nombre de matiere d un professeur',
            'Créer un professeur',
            'Modifier un professeur',
            'Supprimer un professeur',
            'Voir liste des professeurs',
            'Voir details professeur',
            'Voir le nombre de professeur',
            'Voir la liste des professeur et leurs matieres',
            'Attribuer des matieres à un professeur',
            'Voir liste de chaque matiere  et le professeur',
            'Créer une annee scolaire',
            'Modifier une annee scolaire',
            'Supprimer une annee scolaire',
            'Voir liste des annees scolaires',
            'Voir details annee scolaire',
            'Créer une classe',
            'Modifier une classe',
            'Supprimer une classe',
            'Voir liste des classes',
            'Voir details classe',
            'Voir la liste des années scolaires et leurs classes',
            'Attribution des classes pour une année scolaire ouverte',
            'Voir le nombre de classe pour l année en cours',
            'Voir Liste des niveaux des classes pour une année scolaire',
            'Voir liste des professeurs avec leurs matieres et leurs classes',
            'Attribution des classes aux professeurs',
            'Voir le nombre de classe pour un professeur',
            'Ecrire dans le cahier de texte',
            'Modifier le contenu d un cahier de texte',
            'Supprimer le contenu d un cahier de texte',
            'Voir le cahier de texte d une classe',
            'Ajouter une évaluation',
            'Modifier une évaluation',
            'Supprimer une évaluation',
            'Voir la liste des évaluations pour d une classe',
            'Voir les évaluations du jour',
            'Voir la liste des évaluations pour un eleve',
            'Ajouter une horaire',
            'Modifier une horaire',
            'Supprimer une horaire',
            'Voir la liste des horaires pour une classe',
            'Voir la liste des horaires pour un professeur',
            'Ajouter un eleve et son parent',
            'Modifier un eleve',
            'Supprimer un eleve',
            'Voir les detaisl d un eleve',
            'Voir nombre eleve',
            'Voir la liste des eleves pour un parent',
            'Voir la liste des eleves pour une classe',
            'Attibuer des eleves à des classes',
            'Nombre d eleve pour un parent',
            'Voir la liste des absences pour une classe',
            'Voir les absences par eleve',
            'Marquer une absence',
            'Créer une note',
            'Modifier une note',
            'Supprimer une note',
            'Voir la liste des notes pour une matiere',
            'Voir la liste des notes pour un eleve',
        ];

        // Créer les permissions avec le guard spécifié
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        // Vérifier si les rôles existent avant de les créer
        $roles = ['admin', 'professeur', 'parent', 'eleve'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
        }

        // Attribution des permissions aux rôles
        $roleAdmin = Role::findByName('admin', $guard);
        $roleProfesseur = Role::findByName('professeur', $guard);
        $roleParent = Role::findByName('parent', $guard);
        $roleEleve = Role::findByName('eleve', $guard);

        // Permissions pour admin
        $roleAdmin->givePermissionTo($permissions);

        // Permissions pour professeur
        $permissionsProfesseur = [
            'Voir liste des matieres',
            'Voir details matiere',
            'Voir le nombre de matiere d un professeur',
            'Voir le nombre de classe pour un professeur',
            'Voir la liste des horaires pour une classe',
            'Ecrire dans le cahier de texte',
            'Modifier le contenu d un cahier de texte',
            'Supprimer le contenu d un cahier de texte',
            'Voir le cahier de texte d une classe',
            'Ajouter une évaluation',
            'Modifier une évaluation',
            'Supprimer une évaluation',
            'Voir la liste des évaluations pour d une classe',
            'Voir la liste des notes pour une matiere',
            'Voir la liste des horaires pour un professeur',
            'Voir la liste des absences pour une classe',
            'Marquer une absence',
            'Créer une note',
            'Modifier une note',
            'Supprimer une note',
            'Voir la liste des notes pour une matiere',
        ];
        $roleProfesseur->givePermissionTo($permissionsProfesseur);

        // Permissions pour parent
        $permissionsParent = [
            'Voir la liste des eleves pour un parent',
            'Nombre d eleve pour un parent',
            'Voir les absences par eleve',
            'Voir la liste des notes pour un eleve',
            'Voir les évaluations du jour',
            'Voir la liste des horaires pour une classe',
            'Voir le cahier de texte d une classe',
            'Voir la liste des évaluations pour d une classe',
            'Voir les detaisl d un eleve',
        ];
        $roleParent->givePermissionTo($permissionsParent);

        // Permissions pour eleve
        $permissionsEleve = [
            'Voir la liste des notes pour un eleve',
            'Voir les évaluations du jour',
            'Voir la liste des horaires pour une classe',
            'Voir le cahier de texte d une classe',
            'Voir la liste des évaluations pour d une classe',
            'Voir les detaisl d un eleve',
        ];

        $roleEleve->givePermissionTo($permissionsEleve);
    }
    
}
