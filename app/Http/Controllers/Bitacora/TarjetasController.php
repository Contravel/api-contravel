<?php

namespace App\Http\Controllers\Bitacora;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\bitacora\Tarjetas;
use Exception;
use Illuminate\Support\Facades\Validator;

class TarjetasController extends ApiController
{
    public function saveTarjeta(Request $request)
    {
        // Validar datos
        $validated = Validator::make($request->all(), [
            'id_bitacora' => 'required|integer',
            'tarjeta' => 'required|string',
            'vencimiento' => 'required|string',
            'cvv' => 'required|string|max:4',
            'tipo_tarjeta' => 'required|string|max:50',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            // Cifrar el número de tarjeta
            $encrypt_method = "AES-128-ECB";
            $key = $request->vencimiento;
            $encryptedCard = openssl_encrypt($request->tarjeta, $encrypt_method, $key);

            // Guardar en la base de datos
            $tarjeta = new Tarjetas();
            $tarjeta->id_bitacora = $request->id_bitacora;
            $tarjeta->encrypt = $encryptedCard;
            $tarjeta->vencimiento = $request->vencimiento;
            $tarjeta->cvv = $request->cvv;
            $tarjeta->tipo_tarjeta = $request->tipo_tarjeta;
            $tarjeta->save();

            return response()->json([
                'success' => true,
                'message' => 'Tarjeta guardada correctamente'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la tarjeta', $e->getMessage(), 500);
        }
    }

    public function deleteTarjetaByBitacora(Request $request)
    {
        // Validar el ID recibido
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            // Eliminar registros relacionados a ese id_bitacora
            Tarjetas::where('id_bitacora', $request->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tarjetas eliminadas correctamente',
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar tarjetas', $e->getMessage(), 500);
        }
    }

    public function obtenerTarjeta(Request $request)
    {
        // Validación del parámetro recibido
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $id = $request->input('id');

            // Buscar la tarjeta
            $tarjeta = Tarjetas::where('id_bitacora', $id)->first();

            if (!$tarjeta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarjeta no encontrada'
                ], 404);
            }

            // Desencriptar campo `encrypt`
            $encrypt_method = "AES-128-ECB";
            $key = $tarjeta->vencimiento;
            $tarjeta->encrypt = openssl_decrypt($tarjeta->encrypt, $encrypt_method, $key);

            return response()->json([
                'success' => true,
                'data' => $tarjeta
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener la tarjeta', $e->getMessage(), 500);
        }
    }
}