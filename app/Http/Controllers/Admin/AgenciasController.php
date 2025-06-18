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

            return $this->successResponse('Clientes obtenidos correctamente', $clientes);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los clientes', $e->getMessage(), 500);
        }
    }
}