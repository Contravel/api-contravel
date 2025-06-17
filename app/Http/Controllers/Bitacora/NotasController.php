<?php

namespace App\Http\Controllers\Bitacora;

use Exception;
use App\Models\bitacora\Notas;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotasController extends ApiController
{
    // Método original que espera un Request
    public function saveNotas(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id_bitacora' => 'required|integer',
            'nota' => 'required|string',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $usuario = auth()->user();

            $nota = new Notas();
            $nota->id_bitacora = $request->id_bitacora;
            $nota->nota = $request->nota;
            $nota->user = $usuario->usuario ?? 'sistema';

            $nota->save();

            return response()->json([
                'success' => true,
                'message' => 'Nota guardada correctamente'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la nota', $e->getMessage(), 500);
        }
    }

    // Método reutilizable desde otros controladores
    public static function guardarNotaDirecta($notaTexto, $idBitacora)
    {
        try {
            $usuario = auth()->user();

            $nota = new Notas();
            $nota->id_bitacora = $idBitacora;
            $nota->nota = $notaTexto;
            $nota->user = $usuario->usuario ?? 'sistema';

            $nota->save();
            return true;
        } catch (Exception $e) {
            Log::error('Error al guardar nota directa: ' . $e->getMessage());
            return false;
        }
    }
    public function obtenerNotas(Request $request)
    {
        // Validar el ID recibido
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $id = $request->id;

            // Consultar las notas ordenadas por ID descendente
            $notas = Notas::where('id_bitacora', $id)
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $notas
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener las notas', $e->getMessage(), 500);
        }
    }
}