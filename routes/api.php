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
use App\Http\Controllers\CRM\CatalogoAerolineas;
use App\Http\Controllers\CRM\CatalogoIataController;
use App\Http\Controllers\CRM\ComisionesTempController;
use App\Http\Controllers\CRM\ConveniosAereos;
use App\Http\Controllers\CRM\CorreoController;
use App\Http\Controllers\CRM\ExcepcionProveedores;
use App\Http\Controllers\CRM\PostAdmin;
use App\Http\Controllers\CRM\PostAutos;
use App\Http\Controllers\CRM\PostHoteles;
use App\Http\Controllers\CRM\PostTrenes;
use App\Http\Controllers\CRM\PostValidation;
use App\Http\Controllers\CRM\ReembolsosEspeciales;
use App\Http\Controllers\CRM\ReservasAereasController;
use App\Http\Controllers\Operadora\CatalogController;
use App\Http\Controllers\Operadora\SesionController;
use App\Http\Controllers\ReCaptchaController;

///////////////PETICIONES PARA RECPATCHA V2 Y V3///////////////////////
Route::prefix('recaptcha')->group(function () {
    Route::post('validate-recaptcha-v2', [ReCaptchaController::class, 'validateToken2'])->name('api.recaptcha.v2');
    Route::post('validate-recaptcha', [ReCaptchaController::class, 'validateToken'])->name('api.recaptcha.v3');
});

Route::prefix('login')->group(function () {
    Route::post('v1', [LoginController::class, 'loginContravel'])->name('api.contravel.login');
    Route::post('v2', [LoginController::class, 'loginAgencies'])->name('api.agencies.login');
    Route::get('renewToken', [LoginController::class, 'renewToken'])->middleware('check.bearer')->name('api.contravel.renew');
    Route::get('getDataUser', [SesionController::class, 'getDataUser'])->middleware('check.bearer')->name('api.contravel.user');
});

///////////////PETICIONES PROYECTO OPERADORA///////////////////
Route::prefix('operadora')->group(function () {
    Route::post('sso_login', [SesionController::class, 'sso_sesion'])->name('operadora.contravel.sso');
    Route::get('getDataUser', [SesionController::class, 'getDataUser'])->middleware('check.bearer')->name('operadora.contravel.user');
    Route::get('getServer', [SesionController::class, 'getServer'])->name('operadora.contravel.server');
    Route::get('getAgencyUser', [SesionController::class, 'getAgencyUser'])->middleware('check.bearer')->name('operadora.contravel.user');


    Route::post('updateStatus', [CatalogController::class, 'updateStatus'])->name('api.operadora.updateStatus');
    Route::get('getLastestOffers', [CatalogController::class, 'getLastestOffers'])->name('api.operadora.getLastestOffers');
    Route::get('getCatalogDigital', [CatalogController::class, 'getCatalogDigital'])->name('api.operadora.getCatalogDigital');
    Route::get('getCircuit', [CatalogController::class, 'getCircuit'])->name('api.operadora.getCircuit');
    Route::post('getByRegion', [CatalogController::class, 'getByRegion'])->name('api.operadora.getByRegion');
    Route::post('getCountries', [CatalogController::class, 'getCountries'])->name('api.operadora.getCountries');
    Route::post('getByCountry', [CatalogController::class, 'getByCountry'])->name('api.operadora.getByCountry');
    Route::get('getCruises', [CatalogController::class, 'getCruises'])->name('api.operadora.getCruises');
    Route::get('getTrains', [CatalogController::class, 'getTrains'])->name('api.operadora.getTrains');
    Route::get('getVisa', [CatalogController::class, 'getVisa'])->name('api.operadora.getVisa');
    Route::post('sendMailVisa', [CatalogController::class, 'sendMailVisa'])->name('api.operadora.sendVisaRequest');
    Route::post('getDetails', [CatalogController::class, 'getDetails'])->name('api.operadora.getDetails');
    Route::post('sendMailOffer', [CatalogController::class, 'sendMailOffer'])->name('api.operadora.sendOfferRequest');
    Route::get('download', [CatalogController::class, 'download'])->name('api.operadora.download');
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
    Route::post('saveBoletos', [BoletosController::class, 'saveBoletos'])->name('api.agencias.boletos');
    Route::post('saveBoleto', [BoletosController::class, 'saveBoleto'])->name('api.agencias.saveboleto');
    Route::post('obtenerTarjeta', [TarjetasController::class, 'obtenerTarjeta'])->name('api.agencias.obtarjetas');
    Route::post('obtenerNotas', [NotasController::class, 'obtenerNotas'])->name('api.agencias.obnotas');
    Route::post('obtenerEstatus', [SeguimientosController::class, 'ObtenerEstatus'])->name('api.agencias.obstatus');
    Route::post('obtenerCargoByServicio', [CargosController::class, 'obtenerCargoByServicio'])->name('api.agencias.obcxs');
    Route::post('obtenerBoletos', [BoletosController::class, 'obtenerBoletos'])->name('api.agencias.obboletos');
    Route::get('generaReporte', [ReporteController::class, 'crearReporte'])->name('api.agencias.reporte');
    Route::post('eliminarBoleto', [BoletosController::class, 'eliminarBoleto'])->name('api.agencias.delboletos');
    Route::get('obtenerTipoPago', [TipoPagoController::class, 'obtenerPagos'])->name('api.agencias.pagos');
    Route::get('obtenerServicios', [ResourceController::class, 'obtenerServicios'])->name('api.agencias.servicios');
    Route::get('obtenerPermisos', [PermisosController::class, 'obtenerPermisos'])->name('api.agencias.permisos');
    Route::get('obtenerCargos', [CargosController::class, 'obtenerCargos'])->name('api.agencias.obcargo');
    Route::get('obtenerBitacoras', [SeguimientosController::class, 'obtenerBitacoras'])->name('api.agencias.obbitacora');
    Route::get('obtenerAgencias', [AgenciasController::class, 'obtenerClientes'])->name('api.agencias.obbitacora');
});



Route::prefix('crm')->group(function () {
    //Autos
    Route::post('/ObtenerComisionesAutos', [PostAutos::class, 'obtenerComisiones']);
    Route::post('/upAutos', [PostAutos::class, 'createDesgloseAutos']);
    Route::get('/obtenerProvCars', [PostAutos::class, 'getProvCars']);
    Route::post('/upProveedorAutos', [PostAutos::class, 'agregarProveedorAutos']);
    //Trenes
    Route::get('/obtenerProveedoresTrenes', [PostTrenes::class, 'getProveedoresTrenes']);
    Route::post('/upProveedorTrenes', [PostTrenes::class, 'agregarProveedorTrenes']);
    Route::post('/upTrenes', [PostTrenes::class, 'createDesgloseTrenes']);
    Route::post('/CancelReserv', [PostHoteles::class, 'postCancel']);
    //Hoteles
    Route::post('/MandarObs', [PostHoteles::class, 'postOBS']);
    Route::get('/reportConsult', [PostHoteles::class, 'getReport']);
    Route::post('/upHoteles', [PostHoteles::class, 'createDesglose']);
    Route::post('/upProveedor', [PostHoteles::class, 'agregarProveedor']);
    Route::get('/obtenerProveedores', [PostHoteles::class, 'getProveedores']);
    Route::post('/ReservasPagadas', [PostHoteles::class, 'postAllReserv']);
    Route::post('/ReservasOperador', [PostHoteles::class, 'postReservOperador']);
    Route::post('/ReservasConfirmadas', [PostHoteles::class, 'postAllConfir']);
    Route::post('/ObtenerReserva', [PostHoteles::class, 'postReserva']);
    Route::post('/ConsultarReserva', [PostHoteles::class, 'postConsultReserva']);
    Route::post('/ConfirmarReserva', [PostHoteles::class, 'postConfi']);
    Route::post('/InsertConfir', [PostHoteles::class, 'postInsert']);
    Route::post('/ConsultReser', [PostHoteles::class, 'postConsult']);

    //Validation
    Route::post('/login', [PostValidation::class, 'postLogin']);
    Route::post('/logout', [PostValidation::class, 'postLogout']);

    //Users
    Route::get('/obtenerUsers', [PostValidation::class, 'getUsers']);
    Route::get('/obtenerPermisos', [PostValidation::class, 'getPermisos']);
    Route::post('/UpdatePermisos', [PostValidation::class, 'postPermiso']);
    //Mail
    Route::post('/enviarCorreo', [CorreoController::class, 'enviar']);
    //Docs
    Route::post('/upload', [PostAdmin::class, 'almacenarImg']);
    Route::post('/uploadPDF', [PostAdmin::class, 'almacenarPDF']);
    //ReservasGenerales
    Route::post('/ConsultAgencias', [PostAdmin::class, 'postAgencias']);
    Route::post('/ConsultCargosTPV', [PostAdmin::class, 'postCargosTPV']);

    Route::get('/reembolsos-especiales', [ReembolsosEspeciales::class, 'index']);
    Route::post('/reembolsos-especiales', [ReembolsosEspeciales::class, 'store']);
    Route::put('/reembolsos-especiales/{id}', [ReembolsosEspeciales::class, 'update']);

    Route::get('/proveedores-excepciones', [ExcepcionProveedores::class, 'index']);
    Route::post('/proveedores-excepciones', [ExcepcionProveedores::class, 'store']);
    Route::put('/proveedores-excepciones/{id}', [ExcepcionProveedores::class, 'update']);
    Route::delete('/proveedores-excepciones/{id}', [ExcepcionProveedores::class, 'destroy']);
    Route::post('/comisiones/upload-temp', [ComisionesTempController::class, 'uploadTemp']);
    Route::get('/comisiones/temp/{id}', [ComisionesTempController::class, 'getTemp']);
    Route::put('/comisiones/temp/{id}', [ComisionesTempController::class, 'updateTemp']);
    Route::delete('/comisiones/temp/{tempId}', [ComisionesTempController::class, 'destroy']);

    Route::get('/convenios-aereos', [ConveniosAereos::class, 'index']);
    Route::post('/convenios-aereos', [ConveniosAereos::class, 'store']);
    Route::patch('/convenios-aereos/{id}', [ConveniosAereos::class, 'update']);
    Route::delete('/convenios-aereos/{id}', [ConveniosAereos::class, 'destroy']);
    Route::put('/convenios-aereos/reordenar', [ConveniosAereos::class, 'reordenar']);

    Route::get('/catalogo-aerolineas', [CatalogoAerolineas::class, 'index']);
    Route::post('/catalogo-aerolineas', [CatalogoAerolineas::class, 'store']);
    Route::put('/catalogo-aerolineas/{id}', [CatalogoAerolineas::class, 'update']);
    Route::delete('/catalogo-aerolineas/{id}', [CatalogoAerolineas::class, 'destroy']);



    Route::get('/reservas-aereas', [ReservasAereasController::class, 'index']);
    Route::post('/reservas-aereas', [ReservasAereasController::class, 'store']);
    Route::put('/reservas-aereas/{id}', [ReservasAereasController::class, 'update']);
    Route::delete('/reservas-aereas/{id}', [ReservasAereasController::class, 'destroy']);


    Route::get('/catalogo-iata', [CatalogoIataController::class, 'index']);
    Route::get('/catalogo-iata/{code}', [CatalogoIataController::class, 'show']);
    Route::get('/catalogo-iata-search', [CatalogoIataController::class, 'search']);
});
