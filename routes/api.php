<?php

use App\Http\Controllers\abonnementController;
use App\Http\Controllers\api\collecteDeFondController;
use App\Http\Controllers\api\PayementController;
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
    Route::get('/voirHistoriqueDon',[UsersController::class,"VoirhistoriqueDesDonsPourUnDonateur"]);
    Route::get('/voirListeDonateurADesDons',[UsersController::class,"listeDonateurADesDons"]);
    Route::get('/logout',[UsersController::class,"logout"]);
});
    

Route::middleware(['auth:api','donateur'])->group(function(){
    Route::post('/faireUnDon', [PayementController::class, 'initiatePayment']);
    //debut route de l'api paytech
    Route::get('payments', [PayementController::class, 'index'])->name('payment.index');
    Route::post('/checkout', [PayementController::class, 'payment'])->name('payment.submit');
    Route::get('ipn', [PayementController::class, 'ipn'])->name('paytech-ipn');
    Route::get('payment-cancel', [PayementController::class, 'cancel'])->name('paytech.cancel');
    //fin route de l'api paytech
    Route::get('/listeCollecte',[collecteDeFondController::class,"listeCollecte"]);
    Route::put('/supprimerCompte',[UsersController::class,"supprimerCompte"]);
    Route::post('/modifierProfil',[collecteDeFondController::class,"modifierProfil"]);
    Route::get('/historiqueDons',[collecteDeFondController::class,"historiqueDesDonsPourUnDonateur"]);
    Route::get('/historiqueDon/{donId}',[collecteDeFondController::class,"historiqueDonPourUnDonateur"]);
    Route::post('/sabonner/{fondationId}',[abonnementController::class,"sabonner"]);
    Route::post('/sedesabonner/{fondationId}',[abonnementController::class,"sedesabonner"]);
    Route::get('/logout',[UsersController::class,"logout"]);
});
    //les deux sortie de l'api paytech sorti du middleware
    Route::get('payment-success/{code}', [PayementController::class, 'success'])->name('payment.success');
    Route::get('payment/{code}/success', [PayementController::class, 'paymentSuccessView'])->name('payment.success.view');


Route::middleware(['auth:api','fondation'])->group(function(){
    Route::post('/creerCollecte',[collecteDeFondController::class,"store"]);
    Route::post('/modifierCollecte/{collecteDeFond}',[collecteDeFondController::class,"update"]);
    Route::delete('/supprimerCollecte/{collecteDeFond}',[collecteDeFondController::class,"destroy"]);
    Route::post('/modifierProfil',[collecteDeFondController::class,"modifierProfil"]);
    Route::put('/cloturerUneCollecte/{collecteDeFond}',[collecteDeFondController::class,"cloturerUneCollecte"]);
    Route::put('/decloturerUneCollecte/{collecteDeFond}',[collecteDeFondController::class,"decloturerUneCollecte"]);
    Route::get('/listeCollecteEnCours',[collecteDeFondController::class,"listeCollecteEnCours"]);
    Route::get('/listeCollecteCloturer',[collecteDeFondController::class,"listeCollecteCloturer"]);
    Route::put('/supprimerCompte',[UsersController::class,"supprimerCompte"]);
    Route::get('/listeDonateurADesDons',[collecteDeFondController::class,"listeDonateurADesDons"]);
    Route::get('/listeDonateurAUnDon/{collecteId}',[collecteDeFondController::class,"listeDonateurAUnDon"]);
    Route::get('/listeAbonner',[abonnementController::class,"listeAbonnerAUneFondation"]);

    Route::get('/logout',[UsersController::class,"logout"]);
});

 Route::post('/register', [UsersController::class,'store']);

 Route::post('/login', [UsersController::class,'login']);

