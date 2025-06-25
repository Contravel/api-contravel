<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\ApiController;
use App\Models\bitacora\Boletos;
use App\Models\bitacora\Notas;
use App\Models\bitacora\Seguimientos;
use App\Models\bitacora\Servicio;
use App\Models\bitacora\Tarjetas;
use App\Traits\TokenManage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'notas'        => 'required',
            'listBoletos'  => 'required|array',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        DB::connection('mysql3')->beginTransaction();
        try {
            $resource = new ResourceController();
            $function = $resource->getUser($request)->getContent();
            $data = json_decode($function, true);
            $user = $data['data']['token']['sub'];
            // 1. Guardar seguimiento
            $bitacora = new Seguimientos();
            $bitacora->pnr = $request->dataBitacora['pnr'];
            $bitacora->cve_agencia = $request->dataBitacora['cveAgencia'];
            $bitacora->nombre_agencia = $request->dataBitacora['nomCliente'];
            $bitacora->user = $user;
            $bitacora->id_servicio = $request->dataBitacora['servicio'];
            $bitacora->estatus = 1;
            $bitacora->save();

            // 2. Guardar boletos
            foreach ($request->listBoletos as $boleto) {
                $boleto['id_bitacora'] = $bitacora->id;
                $boleto['id_boleto'] = $boleto['boleto'];
                $boleto['concepto'] = $boleto['cargo'];
                $boleto['cargo'] = round($boleto['precio'] * 1.16);
                
                Boletos::create($boleto);
            }

            // 3. Guardar tarjeta
            $tarjeta = $request->dataBank;
            $encrypt_method = "AES-128-ECB";
            $encrypt = openssl_encrypt($tarjeta['tarjeta'], $encrypt_method, $tarjeta['vencimiento']);
            $tarjeta['id_bitacora'] = $bitacora->id;
            $tarjeta['tipo_tarjeta'] = $tarjeta['tipoTarjeta'];
            $tarjeta['encrypt'] = $encrypt;
            Tarjetas::create($tarjeta);

            // 4. Guardar nota
            $nota = new Notas();
            $nota->id_bitacora = $bitacora->id;
            $nota->nota = $request->notas;
            $nota->user = $user;
            $nota->save();

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
