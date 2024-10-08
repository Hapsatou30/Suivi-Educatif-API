<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\HoraireController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ClasseProfController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\AnneeClasseController;
use App\Http\Controllers\CahierTexteController;
use App\Http\Controllers\ClasseEleveController;
use App\Http\Controllers\EvaluationsController;
use App\Http\Controllers\ProfMatiereController;
use App\Http\Controllers\AnneeScolaireController;
use App\Models\AnneeClasse;
use App\Models\ClasseEleve;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//route pour la connexion
Route::post('login', [ApiController::class, 'login']);

//route avec le middleware pour la connexion
Route::middleware(['auth:api'])->group(function () {
    //route pour la déconnexion
    Route::get('logout', [ApiController::class, 'logout']);
    //route pour le rafraichir du token
    Route::get('refresh', [ApiController::class,'refresh']);

     //route pour voir le profile
     Route::get('profile', [ApiController::class, 'profile']);
 
});




Route::group ([ "middleware" => ["auth"] ],  function(){

    //role admin
    Route::middleware(['auth', 'role:admin'])->group(function () {
    //route pour les matieres
    Route::post('matieres' , [MatiereController::class, 'store']);
    Route::put('matieres/{matiere}', [MatiereController::class, 'update']);
    Route::delete('matieres/{matiere}', [MatiereController::class, 'destroy']);

    //route pour les professeurs
    Route::get('professeurs', [ProfesseurController::class, 'index']);
    Route::post('professeurs', [ProfesseurController::class,'store']);
    // Route::put('professeur/{professeur}', [ProfesseurController::class, 'update']);
    Route::delete('professeur/{professeur}', [ProfesseurController::class, 'destroy']);

    //route pour les prof matieres
    Route::post('professeur-matieres', [ProfMatiereController::class,'store']);
    Route::put('professeur-matieres/{professeur_matiere}', [ProfMatiereController::class,'update']);
    Route::delete('professeur-matieres/{professeur_matiere}', [ProfMatiereController::class,'destroy']);

    //route pour les années scolaires
    Route::get('annees-scolaires/{annee_scolaires}' , [AnneeScolaireController::class, 'show']);
    Route::get('annees-scolaires', [AnneeScolaireController::class,'index']);
    Route::post('annees-scolaires', [AnneeScolaireController::class,'store']);
    Route::put('annees-scolaires/{annees_scolaire}', [AnneeScolaireController::class,'update']);
    Route::delete('annees-scolaires/{annees_scolaire}', [AnneeScolaireController::class,'destroy']);

    //route pour les classes
    Route::get('classes/{class}' ,[ClasseController::class, 'show']);
    Route::get('classes', [ClasseController::class,'index']);
    Route::post('classes', [ClasseController::class,'store']);
    Route::put('classes/{class}', [ClasseController::class,'update']);
    Route::delete('classes/{class}', [ClasseController::class,'destroy']);

    //route pour les années-classes
   
    Route::get('annees-classes', [AnneeClasseController::class,'index']);
    Route::post('annees-classes', [AnneeClasseController::class,'store']);
    Route::put('annees-classes/{annees_class}', [AnneeClasseController::class,'update']);
    Route::delete('annees-classes/{annees_class}', [AnneeClasseController::class,'destroy']);

    //route pour les classes-professeurs
    Route::post('classes-professeurs', [ClasseProfController::class,'store']);
    Route::put('classes-professeurs/{classes_professeur}', [ClasseProfController::class,'update']);
    Route::delete('classes-professeurs/{classes_professeur}', [ClasseProfController::class,'destroy']);

    //route pour les horaires
    Route::post('horaires', [HoraireController::class,'store']);
    Route::put('horaires/{horaire}', [HoraireController::class,'update']);
    Route::delete('horaires/{horaire}', [HoraireController::class,'destroy']);

    //route pour les élèves
    Route::post('eleves', [EleveController::class,'store']);
    Route::put('eleves/{elefe}', [EleveController::class,'update']);
    Route::delete('eleves/{elefe}', [EleveController::class,'destroy']);

    //route pour les parents
    Route::get('parents', [ParentsController::class,'index']);
    Route::post('parents', [ParentsController::class,'store']);
    Route::put('parents/{parent}', [ParentsController::class,'update']);
    Route::delete('parents/{parent}', [ParentsController::class,'destroy']);

    //route pour les classes-eleves
    Route::post('classes-eleves', [ClasseEleveController::class,'store']);
    Route::put('classes-eleves/{classes_elefe}', [ClasseEleveController::class,'update']);
    Route::delete('classes-eleves/{classes_elefe}', [ClasseEleveController::class,'destroy']);
    Route::get('classeEleve', [ClasseEleveController::class, 'classeEleve']);
    //route pour les niveau classe
    Route::get('annees/{anneeId}/niveaux', [AnneeClasseController::class, 'niveauClasses']);

      //route pour le total d'élèves
   Route::get('total-eleves', [ClasseEleveController::class, 'totalEleves']);

   //route pour le total de professeurs
   Route::get('total-professeurs', [ProfesseurController::class, 'totalProfesseurs']);

   //route pour le nombre de classes ouvertes
   Route::get('nombre-classes', [AnneeClasseController::class, 'nombreClasseOuverte']);


   //route pour les evaluations du jour
   Route::get('evaluations-jour', [EvaluationsController::class, 'evaluationsJour']);

   //route pour les profMat
   Route::get('prof-matieres',[ ProfMatiereController::class,'profMat']);

   //route pour voir la liste des matieres et classe pour un prof
   Route::get('professeur/{id}/classes-matieres', [ClasseProfController::class, 'showProfMatiereClasse']);

   //route pour la liste de tous les elves
   Route::get('listeleves', [ EleveController::class, 'eleves']);


    });


//role pour professeur et admin 
Route::middleware(['auth', 'role:professeur|admin'])->group(function () {
    Route::post('professeur/{professeur}', [ProfesseurController::class, 'update']);
});

    //role professeur
    Route::middleware(['auth', 'role:professeur'])->group(function () {

        //route pour les evaluations
        Route::post('evaluations', [EvaluationsController::class,'store']);
        Route::put('evaluations/{evaluation}', [EvaluationsController::class,'update']);
        Route::delete('evaluations/{evaluation}', [EvaluationsController::class,'destroy']);
        Route::get('evaluations/professeur/{professeurId}', [EvaluationsController::class, 'evaluationsParProfesseur']);
        Route::get('evaluations/classe/{classeProfId}', [EvaluationsController::class, 'evaluationsParClasseProf']);

        //route pour le cahier de texte
        Route::post('cahiers-texte', [CahierTexteController::class,'store']);
        Route::put('cahiers-texte/{cahiers_texte}', [CahierTexteController::class,'update']);
        Route::delete('cahiers-texte/{cahiers_texte}', [CahierTexteController::class,'destroy']);

        //route pour les notes
        Route::post('notes', [NoteController::class,'store']);
        Route::put('notes/{note}', [NoteController::class,'update']);
        Route::delete('notes/{note}', [NoteController::class,'destroy']);

        //route pour les absences
        Route::post('absences', [PresenceController::class,'store']);
        Route::put('absences/{absence}', [PresenceController::class,'update']);
        Route::delete('absences/{absence}', [PresenceController::class,'destroy']);
        
         //nombre de matiere pour un prof
        Route::get('professeur/{id}/nombre-matieres', [MatiereController::class, 'nombreMatieresParProf']);

        //liste des matieres pour un prof
        Route::get('professeur/{id}/matieres', [MatiereController::class,'listeMatieresParProf']);
        //liste des classes du prof
        Route::get( 'professeur/{id}/liste_classes' , [ClasseProfController::class, 'listeClassesParProf']);

        //nombre de classes pour un prof
        Route::get('professeurs/{professeurId}/classes', [ClasseProfController::class, 'nombreClassesParProf']);


    });

    //role parent et eleve
    Route::middleware(['auth', 'role:parent|eleve'])->group(function () {

        //liste des eleves regroupes par parent
    Route::get('parents/{parent_id}/eleves', [ClasseEleveController::class, 'elevesParParent']);
    
    //details d'un eleve
    Route::get('eleve/{classeEleve}', [ClasseEleveController::class, 'show']);

    //nombre eleves par parent
    Route::get('parents/{parent_id}/nombre-eleves', [ClasseEleveController::class, 'nombreElevesParParent']);

    Route::get('parents/{parentId}/absences', [PresenceController::class, 'getAbsencesParParent']);

    //note pour un eleve
    Route::get('eleves/{classeEleve_id}/notes', [NoteController::class, 'noteEleve']);

    //evaluations pour un eleve
    Route::get('eleves/{eleveId}/evaluations', [EvaluationsController::class, 'evaluationsEleve']);

    //liste des evaluations 
    Route::get('evaluations/eleves/{parentId}' , [EvaluationsController::class, 'evaluationsEleveParent']);


    });


    //route pour les matières
    // Route::apiResource('matieres', MatiereController::class);
    Route::get('matieres' , [MatiereController::class, 'index']);
   
    Route::get('matieres/{matiere}', [MatiereController::class, 'show']);

    //route pour les professeurs
    // Route::apiResource('professeurs', ProfesseurController::class);
   
    Route::get('professeur/{professeur}', [ProfesseurController::class, 'show']);

    //route pour les professeur-matiere
    // Route::apiResource('professeur-matieres', ProfMatiereController::class);
    Route::get('professeur-matieres/{professeur_matiere}', [ProfMatiereController::class,'show']);
    Route::get('professeur-matieres', [ProfMatiereController::class,'index']);
   

    //route pour les années scolaires
    // Route::apiResource('annees-scolaires', AnneeScolaireController::class);
   
    //route pour les classes
    // Route::apiResource('classes', ClasseController::class);
  

    //route pour les années-classes
    // Route::apiResource('annees-classes', AnneeClasseController::class);
    Route::get('annees-classes/{annees_class}', [AnneeClasseController::class,'show']);
   
         //route pour les notes par matieres
 Route::get('notes/classe/{classe_prof_id}', [NoteController::class, 'index']);

       
    //route pour les classes-professeurs
    // Route::apiResource('classes-professeurs', ClasseProfController::class);
    Route::get('classes-professeurs/{anneeClasseId}', [ClasseProfController::class,'showProfMatiereClasse']);
    Route::get('classes-professeurs', [ClasseProfController::class,'index']);
    



    //route pour les élèves
    // Route::apiResource('eleves', EleveController::class);
    Route::get('eleves/{elefe}', [EleveController::class,'show']);
    Route::get('eleves', [EleveController::class,'index']);
   

    //route pour les parents
    // Route::apiResource('parents', ParentsController::class);
    Route::get('parents/{parent}', [ParentsController::class,'show']);
  

    //route pour les classes-élèves
    // Route::apiResource('classes-eleves', ClasseEleveController::class);
    // Route::get('classes-eleves/{classes_elefe}', [ClasseEleveController::class,'show']);
    Route::get('classes-eleves/{anneeClasseId}', [ClasseEleveController::class,'index']);
;

    //routes pour les cahiers de texte
//    Route::apiResource('cahiers_texte', CahierTexteController::class);
   Route::get('cahiers-texte', [CahierTexteController::class,'index']);
  
   Route::get('cahiers-texte/classe/{anneeClasseId}', [CahierTexteController::class, 'cahierParClasse']);

   Route::get('details-cahiers-texte/{id}', [CahierTexteController::class, 'show']);

   //route pour les évaluations
//    Route::apiResource('evaluations', EvaluationsController::class);
   Route::get('evaluations/{evaluation}', [EvaluationsController::class, 'show']);
   Route::get('evaluations', [EvaluationsController::class, 'index']);
   

   //routes pour les horaires
//    Route::apiResource('horaires', HoraireController::class);
   Route::get('horaires/{horaire}', [HoraireController::class,'show']);
   Route::get('horaires', [HoraireController::class,'index']);


     //route pour les notes
    //   Route::apiResource('notes', NoteController::class);
      Route::get('notes/{note}', [NoteController::class,'show']);
    //   Route::get('notes', [NoteController::class,'index']);
      

      //routes pour les prensences
//    Route::apiResource('absences' , PresenceController::class);
//    Route::get('absences/{absence}', [PresenceController::class,'show']);
//    Route::get('absences/{classeEleveId}', [PresenceController::class,'getAbsences']);
   Route::get('absences/{classProfId}' , [PresenceController::class, 'index']);

 
   //horaire d'une classe
   Route::get('annee_classe/{anneeClasseId}/horaires', [HoraireController::class, 'horaireClasse']);

   //horaires pour un prof
   Route::get('professeur/{professeurId}/horaires', [HoraireController::class, 'horaireProf']);

//    Route::get('classe-eleve/{classeEleveId}/absences', [PresenceController::class, 'getAbsences']);


   //voir les années scolaire
   Route::get('annees-scolaires', [AnneeScolaireController::class,'index']);


   Route::get('annee-classes/{anneeClasse}', [AnneeClasseController::class, 'show']);

    //cahier de texte par annee classe id 

    Route::get('evaluations/anneeClasse/{anneeClasseId}', [EvaluationsController::class, 'evaluationsParAnneeClasse']);

    //les absences par année classe 
    Route::get('absences/annee-classe/{anneeClasseId}', [PresenceController::class, 'getAbsencesParAnneeClasse']);


});


