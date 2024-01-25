<?php

use App\Http\Controllers\api\PayementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('payments', [PayementController::class, 'index'])->name('payment.index');
Route::post('/checkout', [PayementController::class, 'payment'])->name('payment.submit');
Route::get('ipn', [PayementController::class, 'ipn'])->name('paytech-ipn');
Route::get('payment-success/{code}', [PayementController::class, 'success'])->name('payment.success');
Route::get('payment/{code}/success', [PayementController::class, 'paymentSuccessView'])->name('payment.success.view');
Route::get('payment-cancel', [PayementController::class, 'cancel'])->name('paytech.cancel');





// Route::middleware(['auth:api','donateur'])->group(function(){
//     Route::get('payment', [PayementController::class, 'index'])->name('payment.index');
//     Route::post('/checkout', [PayementController::class, 'payment'])->name('payment.submit');
//     Route::get('ipn', [PayementController::class, 'ipn'])->name('paytech-ipn');
//     Route::get('payment-success/{code}', [PayementController::class, 'success'])->name('payment.success');
//     Route::get('payment/{code}/success', [PayementController::class, 'paymentSuccessView'])->name('payment.success.view');
//     Route::get('payment-cancel', [PayementController::class, 'cancel'])->name('paytech.cancel');
// });
