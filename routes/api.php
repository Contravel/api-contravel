<?php

use App\Http\Controllers\Bitacora\ResourceController;
use App\Http\Controllers\LoginController;
use App\Models\bitacora\Servicio;
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


Route::prefix('login')->group(function () {
    Route::post('v1', [LoginController::class, 'loginContravel'])->name('api.contravel.login');
    Route::post('v2', [LoginController::class, 'loginAgencies'])->name('api.agencies.login');
});


///////////////PETICIONES PROYECTO BITACORA///////////////////
Route::prefix('bitacora')->group(function () {
    Route::get('getServices', [ResourceController::class, 'getServices'])->middleware('check.bearer')->name('api.bitacora.services');
    Route::get('getUser', [ResourceController::class, 'getUser'])->middleware('check.bearer')->name('api.bitacora.user');
});
