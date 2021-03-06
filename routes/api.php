<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::middleware(['check-permiso'])->group(function () { 
    Route::prefix("user")->group(function(){
        Route::post('registrar',[UsersController::class,'registrar']);
        Route::post('recuperarPass',[UsersController::class,'recuperarPass']);
        Route::get('listarEmpleados',[UsersController::class,'listarEmpleados']);
        Route::get('verEmpleado',[UsersController::class,'verEmpleado']);
        Route::get('verPerfil',[UsersController::class,'verPerfil'])->withoutMiddleware('check-permiso');
        Route::post('modificarEmpleado',[UsersController::class,'modificarEmpleado']);
        Route::put('login',[UsersController::class,'login'])->withoutMiddleware('check-permiso');
    });
    
});

//Route::middleware('check-user')->get(...)
