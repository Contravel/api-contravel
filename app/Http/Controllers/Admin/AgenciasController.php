<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\admin\AgenciasAdmon;
use Exception;


class AgenciasController extends ApiController
{
    public function obtenerClientes()
    {
        try {
            $clientes = AgenciasAdmon::select('NUM_CLIENTE', 'NOMBRE')
                ->where('ESTATUS', 'ACTIVA')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los clientes', $e->getMessage(), 500);
        }
    }
}