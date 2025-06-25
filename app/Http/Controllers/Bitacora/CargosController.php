<?php

namespace App\Http\Controllers\Bitacora;

use Exception;
use App\Models\bitacora\SeguimientoCargos;
use App\Models\bitacora\Cargos;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class CargosController extends ApiController
{
    public function updateCargo(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'bitacora' => 'required',
            'cuenta' => 'required',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $cargo = new SeguimientoCargos();
            $cargo->seguimiento = $request->bitacora;
            $cargo->numCargo = $request->cuenta;
            $cargo->save();

            return $this->successResponse('Cargo actualizado correctamente', $cargo);
        } catch (Exception $e) {
            return $this->errorResponse('Error al actualizar el cargo',  $e->getMessage(), 500);
        }
    }

    public function obtenerCargos()
    {
        try {
            $cargos = Cargos::all();
            return $this->successResponse('Cargos obtenidos correctamente', $cargos);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los cargos',  $e->getMessage(), 500);
        }
    }

    public function obtenerCargoByServicio(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'servicio' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        $servicio = $request->servicio;

        if ($servicio === 3) {
            $servicio = 2;
        }

        try {
            $cargo = Cargos::find($servicio);

            if (!$cargo) {
                return $this->errorResponse('Cargo no encontrado',  'No existe un cargo con el ID proporcionado', 404);
            }

            return $this->successResponse('Cargo obtenido correctamente', $cargo);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el cargo',  $e->getMessage(), 500);
        }
    }
}