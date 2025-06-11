<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\bitacora\Servicio;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Traits\TokenManage;
use Illuminate\Support\Facades\Log;

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
            return $this->errorResponse("Error", $payloadJWT->message , 401);
        }
    }
}
