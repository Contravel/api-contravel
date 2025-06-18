<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Admin\AgenciasController;

use App\Http\Controllers\Bitacora\NotasController;
use App\Http\Controllers\Bitacora\CargosController;
use App\Http\Controllers\Bitacora\BoletosController;
use App\Http\Controllers\Tablero\PermisosController;
use App\Http\Controllers\Bitacora\ResourceController;
use App\Http\Controllers\Bitacora\TarjetasController;
use App\Http\Controllers\Bitacora\TipoPagoController;
use App\Http\Controllers\Bitacora\SeguimientosController;

Route::prefix('login')->group(function () {
    Route::post('v1', [LoginController::class, 'loginContravel'])->name('api.contravel.login');
    Route::post('v2', [LoginController::class, 'loginAgencies'])->name('api.agencies.login');
});


///////////////PETICIONES PROYECTO BITACORA///////////////////
Route::prefix('bitacora')->group(function () {
    Route::get('getServices', [ResourceController::class, 'getServices'])->middleware('check.bearer')->name('api.bitacora.services');
    Route::get('getUser', [ResourceController::class, 'getUser'])->middleware('check.bearer')->name('api.bitacora.user');


    Route::post('updateStatus', [SeguimientosController::class, 'updateStatus'])->name('api.agencias.status');
    Route::post('updateCargo', [CargosController::class, 'updateCargo'])->name('api.agencias.segcargo');
    Route::post('saveTarjeta', [TarjetasController::class, 'saveTarjeta'])->name('api.agencias.tarjetas');
    Route::post('saveNota', [NotasController::class, 'saveNotas'])->name('api.agencias.notas');
    Route::post('saveDataBitacora', [ResourceController::class, 'saveData'])->name('api.agencias.saveData');
    Route::post('saveCotizacionBitacora', [SeguimientosController::class, 'saveCotizacionBitacora'])->name('api.agencias.cotizacion');
    Route::post('saveBoletos', [BoletosController::class, 'saveBoletos'])->name('api.agencias.boletos');
    Route::post('obtenerTipoPago', [TipoPagoController::class, 'obtenerPagos'])->name('api.agencias.pagos');
    Route::post('obtenerTarjeta', [TarjetasController::class, 'obtenerTarjeta'])->name('api.agencias.obtarjetas');
    Route::post('obtenerServicios', [ResourceController::class, 'obtenerServicios'])->name('api.agencias.servicios');
    Route::post('obtenerPermisos', [PermisosController::class, 'obtenerPermisos'])->name('api.agencias.permisos');
    Route::post('obtenerNotas', [NotasController::class, 'obtenerNotas'])->name('api.agencias.obnotas');
    Route::post('obtenerEstatus', [SeguimientosController::class, 'ObtenerEstatus'])->name('api.agencias.obstatus');
    Route::post('obtenerCargos', [CargosController::class, 'obtenerCargos'])->name('api.agencias.obcargo');
    Route::post('obtenerCargoByServicio', [CargosController::class, 'obtenerCargoByServicio'])->name('api.agencias.obcxs');
    Route::post('obtenerBoletos', [BoletosController::class, 'obtenerBoletos'])->name('api.agencias.obboletos');
    Route::post('obtenerBitacoras', [SeguimientosController::class, 'obtenerBitacoras'])->name('api.agencias.obbitacora');
    Route::post('obtenerAgencias', [AgenciasController::class, 'obtenerClientes'])->name('api.agencias.obbitacora');
    Route::post('generaReporte', [ReporteController::class, 'crearReporte'])->name('api.agencias.reporte');
    Route::post('eliminarBoleto', [BoletosController::class, 'eliminarBoleto'])->name('api.agencias.delboletos');
});