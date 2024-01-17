<?php

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
});

Route::middleware(['auth:api','donateur'])->group(function(){

});

Route::middleware(['auth:api','fondation'])->group(function(){

});

 Route::post('/register', [UsersController::class,'store']);

 Route::post('/login', [UsersController::class,'login']);

