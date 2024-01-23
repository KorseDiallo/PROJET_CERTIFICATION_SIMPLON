<?php

use App\Http\Controllers\api\collecteDeFondsController;
use App\Http\Controllers\api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:api','admin'])->group(function(){
    Route::get('/dashboardAdmin',[UsersController::class,"dashboardAdmin"]);
    Route::post('/approuver/{user}',[UsersController::class,"approuverDemande"]);
    Route::post('/refuserDemande/{user}',[UsersController::class,"refuserDemande"]);
    Route::post('/bloquer/{user}',[UsersController::class,"bloquer"]);
    Route::post('/debloquer/{user}',[UsersController::class,"debloquer"]);
    Route::delete('/supprimer/{user}',[UsersController::class,"destroy"]);
    Route::get('/listeDonateur',[UsersController::class,"listeDonateur"]);
    Route::get('/listeFondation',[UsersController::class,"listeFondation"]);
    Route::get('/logout',[UsersController::class,"logout"]);
});

Route::middleware(['auth:api','donateur'])->group(function(){

});

Route::middleware(['auth:api','fondation'])->group(function(){
    Route::post('/creerCollecte',[collecteDeFondsController::class,"store"]);
    Route::put('/modifierCollecte/{collecteDeFond}',[collecteDeFondsController::class,"update"]);
    Route::delete('/supprimerCollecte/{collecteDeFond}',[collecteDeFondsController::class,"destroy"]);
    Route::put('/modifierProfil',[collecteDeFondsController::class,"modifierProfil"]);
    Route::put('/cloturerUneCollecte/{collecteDeFond}',[collecteDeFondsController::class,"cloturerUneCollecte"]);
    Route::put('/decloturerUneCollecte/{collecteDeFond}',[collecteDeFondsController::class,"decloturerUneCollecte"]);
    Route::get('/listeCollecteEnCours',[collecteDeFondsController::class,"listeCollecteEnCours"]);
    Route::get('/listeCollecteCloturer',[collecteDeFondsController::class,"listeCollecteCloturer"]);
    Route::put('/supprimerCompte',[UsersController::class,"supprimerCompte"]);
    Route::get('/logout',[UsersController::class,"logout"]);
});

 Route::post('/register', [UsersController::class,'store']);

 Route::post('/login', [UsersController::class,'login']);

