<?php

namespace App\Http\Controllers\Operadora;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\LoginController;
use App\Models\contravel_bd\Cliente;
use App\Models\tablero\Contravel_user;
use App\Traits\TokenManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SesionController extends ApiController
{
    use TokenManage;
    public function sso_sesion(Request $request)
    {
        Log::debug('Iniciando validación SSO', $request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'user' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'user.required' => 'The user field is required.',
                'password.required' => 'The password field is required.',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return $this->errorResponse('Error de validación', $errors, 422);
        }
        $user = substr(base64_decode($request->input('user')), 10);
        $pass = substr(base64_decode($request->input('password')), 10);
        Log::debug('Credenciales decodificadas', ['user' => $user, 'password' => $pass]);
        $login = new LoginController();

        $response = $login->loginAgencies(new Request([
            'user' => $user,
            'password' => $pass
        ]));
        return $response;
    }

    public function getDataUser(Request $request)
    {
        $payloadJWT = $this->validateToken($request->bearerToken());
        if ($payloadJWT->status === true) {
            $user = Cliente::where('username', $payloadJWT->token->sub)->first();
            Log::debug('Usuario obtenido', ['user' => $user]);
            return $this->successResponse('Usuario obtenido correctamente', $user);
        }

        return $this->errorResponse('Token inválido',  $payloadJWT->message, 401);
    }

    public function getAgencyUser(Request $request){
        $payloadJWT = $this->validateToken($request->bearerToken());
        Log::debug('Validando token para obtener agencia de usuario', ['token' => $payloadJWT]);
        if ($payloadJWT->status === true) {
            $user = Cliente::with('agency')->where('username', $payloadJWT->token->sub)->first();

            return $this->successResponse('Usuario obtenido correctamente', $user);
        }
    }

    public function getServer(Request $request){
        //$host = $request->getHost();
        $host = "//localhost";
        return $this->successResponse('Host obtenido correctamente', $host);
    }
}
