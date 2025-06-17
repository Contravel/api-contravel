<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\ApiController;
use App\Models\bitacora\Servicio;
use Illuminate\Http\Request;
use App\Traits\TokenManage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\bitacora\Seguimientos;
use App\Models\bitacora\Boletos;
use App\Models\bitacora\Tarjetas;
use App\Models\bitacora\Notas;
use Exception;


class ResourceController extends ApiController
{
    use TokenManage;
    public function getServices(Request $request)
    {
        $payloadJWT = $this->validateToken($request->bearerToken());
        if ($payloadJWT->status === true) {
            $services = Servicio::all();
            return $this->successResponse('Services Success', $services);
        } else {
            return $this->errorResponse("Error", $payloadJWT->message, 401);
        }
    }

    public function getUser(Request $request)
    {
        $payloadJWT = $this->validateToken($request->bearerToken());
        Log::info(json_encode($payloadJWT));
        if ($payloadJWT->status === true) {
            return $this->successResponse('User Success', $payloadJWT);
        } else {
            return $this->errorResponse("Error", $payloadJWT->message, 401);
        }
    }



    public function saveData(Request $request)
    {
        // Validación básica (puedes adaptarla a tus reglas exactas)
        $validated = Validator::make($request->all(), [
            'dataBitacora' => 'required|array',
            'dataBank'     => 'required|array',
            'notas'        => 'required|array',
            'listBoletos'  => 'required|array',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        DB::connection('mysql3')->beginTransaction();

        try {
            // 1. Guardar en Seguimientos
            $bitacora = Seguimientos::create($request->dataBitacora);

            // 2. Guardar boletos
            foreach ($request->listBoletos as $boleto) {
                $boleto['id_bitacora'] = $bitacora->id;
                Boletos::create($boleto);
            }

            // 3. Guardar tarjeta
            $tarjeta = $request->dataBank;
            $tarjeta['id_bitacora'] = $bitacora->id;
            Tarjetas::create($tarjeta);

            // 4. Guardar nota
            $nota = $request->notas;
            $nota['id_bitacora'] = $bitacora->id;
            Notas::create($nota);

            DB::connection('mysql3')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Datos guardados correctamente',
                'id_bitacora' => $bitacora->id
            ]);
        } catch (Exception $e) {
            DB::connection('mysql3')->rollBack();
            return $this->errorResponse('Error al guardar los datos', $e->getMessage(), 500);
        }
    }
    public function obtenerServicios()
    {
        try {
            $servicios = Servicio::all();

            return response()->json([
                'success' => true,
                'data' => $servicios
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los servicios', $e->getMessage(), 500);
        }
    }
}