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
        $validated = Validator::make($request->all(), [
            'id_bitacora' => 'required|integer',
            'tarjeta' => 'required|string',
            'vencimiento' => 'required|string',
            'cvv' => 'required|string|max:4',
            'tipo_tarjeta' => 'required|string|max:50',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $encrypt_method = "AES-128-ECB";
            $key = $request->vencimiento;
            $encryptedCard = openssl_encrypt($request->tarjeta, $encrypt_method, $key);

            $tarjeta = new Tarjetas();
            $tarjeta->id_bitacora = $request->id_bitacora;
            $tarjeta->encrypt = $encryptedCard;
            $tarjeta->vencimiento = $request->vencimiento;
            $tarjeta->cvv = $request->cvv;
            $tarjeta->tipo_tarjeta = $request->tipo_tarjeta;
            $tarjeta->save();

            return $this->successResponse('Tarjeta guardada correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la tarjeta',  $e->getMessage(), 500);
        }
    }

    public function deleteTarjetaByBitacora(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            Tarjetas::where('id_bitacora', $request->id)->delete();

            return $this->successResponse('Tarjetas eliminadas correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar tarjetas',  $e->getMessage(), 500);
        }
    }

    public function obtenerTarjeta(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $id = $request->input('id');
            $tarjeta = Tarjetas::where('id_bitacora', $id)->first();

            if (!$tarjeta) {
                return $this->errorResponse('Tarjeta no encontrada',  'No existe arjeta para el ID proporcionado', 404);
            }

            $encrypt_method = "AES-128-ECB";
            $key = $tarjeta->vencimiento;
            $tarjeta->encrypt = openssl_decrypt($tarjeta->encrypt, $encrypt_method, $key);

            return $this->successResponse('Tarjeta obtenida correctamente', $tarjeta);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener la tarjeta',  $e->getMessage(), 500);
        }
    }
}