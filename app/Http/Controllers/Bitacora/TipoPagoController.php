<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\ApiController;
use App\Models\bitacora\TipoPago;
use Exception;

class TipoPagoController extends ApiController
{
    public function obtenerPagos()
    {
        try {
            $pagos = TipoPago::all();
            return $this->successResponse('Pagos obtenidos correctamente', $pagos);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los pagos',  $e->getMessage(), 500);
        }
    }
}