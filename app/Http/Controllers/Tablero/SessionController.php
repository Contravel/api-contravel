<?php

namespace App\Http\Controllers\Tablero;

use App\Http\Controllers\ApiController;
use App\Models\tablero\Contravel_user;
use App\Traits\TokenManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\FlareClient\Api;

class SessionController extends ApiController
{
    use TokenManage;

    public function getDataUser(Request $request)
    {
        $payloadJWT = $this->validateToken($request->bearerToken());
        if ($payloadJWT->status === true) {
            $user = Contravel_user::where('id', $payloadJWT->token->id)->first();
            return $this->successResponse('Usuario obtenido correctamente', $user);
        }

        return $this->errorResponse('Token inválido',  $payloadJWT->message, 401);
    }
}
