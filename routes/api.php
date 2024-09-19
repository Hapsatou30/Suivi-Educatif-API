<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\ClasseProfController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\AnneeClasseController;
use App\Http\Controllers\ClasseEleveController;
use App\Http\Controllers\ProfMatiereController;
use App\Http\Controllers\AnneeScolaireController;

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

});




Route::group ([ "middleware" => ["auth"] ],  function(){

    //route pour les matières
    Route::apiResource('matieres', MatiereController::class);

    //route pour les professeurs
    Route::apiResource('professeurs', ProfesseurController::class);

    //route pour les professeur-matiere
    Route::apiResource('professeur-matieres', ProfMatiereController::class);

    //route pour les années scolaires
    Route::apiResource('annees-scolaires', AnneeScolaireController::class);

    //route pour les classes
    Route::apiResource('classes', ClasseController::class);

    //route pour les années-classes
    Route::apiResource('annees-classes', AnneeClasseController::class);


    //route pour les élèves
    Route::apiResource('eleves', EleveController::class);

    //route pour les parents
    Route::apiResource('parents', ParentsController::class);

    //route pour les classes-professeurs
    Route::apiResource('classes-professeurs', ClasseProfController::class);

    //route pour les classes-élèves
    Route::apiResource('classes-eleves', ClasseEleveController::class);

   

    //route
});


