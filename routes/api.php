<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\MatiereController;

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



//route pour les matières
Route::group ([ "middleware" => ["auth"] ],  function(){

    Route::apiResource('matieres', MatiereController::class);
});


