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
            $pagos = TipoPago::all(); // Recupera todos los registros de tipo_tarjetas
            return response()->json([
                'success' => true,
                'data' => $pagos
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los pagos', $e->getMessage(), 500);
        }
    }
}