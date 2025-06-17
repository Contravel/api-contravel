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
        // Validar datos
        $validated = Validator::make($request->all(), [
            'bitacora' => 'required|integer',
            'cuenta' => 'required|string|max:255',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            // Crear el nuevo registro en la tabla seguimiento_cargo
            $cargo = new SeguimientoCargos();
            $cargo->seguimiento = $request->bitacora;
            $cargo->numCargo = $request->cuenta;
            $cargo->save();

            return response()->json([
                'success' => true,
                'message' => 'Cargo actualizado correctamente'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al actualizar el cargo', $e->getMessage(), 500);
        }
    }
    public function obtenerCargos()
    {
        try {
            $cargos = Cargos::all();

            return response()->json([
                'success' => true,
                'data' => $cargos
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los cargos', $e->getMessage(), 500);
        }
    }
    public function obtenerCargoByServicio(Request $request)
    {
        // Validar el parámetro
        $validated = Validator::make($request->all(), [
            'servicio' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        $servicio = $request->servicio;

        if ($servicio === 3) {
            $servicio = 2;
        }

        try {
            $cargo = Cargos::find($servicio);

            if (!$cargo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cargo no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cargo
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el cargo', $e->getMessage(), 500);
        }
    }
}
