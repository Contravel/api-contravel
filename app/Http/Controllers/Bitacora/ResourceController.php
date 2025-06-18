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
            return $this->successResponse('Servicios obtenidos correctamente', $services);
        }

        return $this->errorResponse('Token inválido',  $payloadJWT->message, 401);
    }

    public function getUser(Request $request)
    {
        $payloadJWT = $this->validateToken($request->bearerToken());

        Log::info(json_encode($payloadJWT));

        if ($payloadJWT->status === true) {
            return $this->successResponse('Usuario obtenido correctamente', $payloadJWT);
        }

        return $this->errorResponse('Token inválido',  $payloadJWT->message, 401);
    }

    public function saveData(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'dataBitacora' => 'required|array',
            'dataBank'     => 'required|array',
            'notas'        => 'required|array',
            'listBoletos'  => 'required|array',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        DB::connection('mysql3')->beginTransaction();

        try {
            // 1. Guardar seguimiento
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

            return $this->successResponse('Datos guardados correctamente',  $bitacora->id);
        } catch (Exception $e) {
            DB::connection('mysql3')->rollBack();
            return $this->errorResponse('Error al guardar los datos',  $e->getMessage(), 500);
        }
    }

    public function obtenerServicios()
    {
        try {
            $servicios = Servicio::all();
            return $this->successResponse('Servicios obtenidos correctamente', $servicios);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los servicios',  $e->getMessage(), 500);
        }
    }
}