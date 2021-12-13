<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['check-permiso'])->get('/user', function () { //api-auth saca de la request el api token; admin ver en el request quien está haciendo la peticition
    Route::put('registrar',[UsersController::class,'registrar']);
    Route::put('login',[UsersController::class,'login'])->withoutMiddleware('check-permiso');
});

//Route::middleware('check-user')->get(...)
