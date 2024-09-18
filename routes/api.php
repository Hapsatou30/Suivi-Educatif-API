<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ProfesseurController;

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
});


