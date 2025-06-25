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
    // Método que guarda una nota desde un Request
    public function saveNotas(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id_bitacora' => 'required|integer',
            'nota' => 'required|string',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $resource = new ResourceController();
            $function = $resource->getUser($request)->getContent();
            $data = json_decode($function, true);
            $user = $data['data']['token']['sub'];

            $nota = new Notas();
            $nota->id_bitacora = $request->id_bitacora;
            $nota->nota = $request->nota;
            $nota->user = $user;
            $nota->save();

            return $this->successResponse('Nota guardada correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la nota',  $e->getMessage(), 500);
        }
    }

    // Método reutilizable desde otros controladores
    public static function guardarNotaDirecta($notaTexto, $idBitacora, $request)
    {
        try {
            $resource = new ResourceController();
            $function = $resource->getUser($request)->getContent();
            $data = json_decode($function, true);
            $user = $data['data']['token']['sub'];

            $nota = new Notas();
            $nota->id_bitacora = $idBitacora;
            $nota->nota = $notaTexto;
            $nota->user = $user;
            $nota->save();

            return true;
        } catch (Exception $e) {
            Log::error('Error al guardar nota directa: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerNotas(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $notas = Notas::where('id_bitacora', $request->id)
                ->orderByDesc('id')
                ->get();

            return $this->successResponse('Notas obtenidas correctamente', $notas);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener las notas',  $e->getMessage(), 500);
        }
    }
}
