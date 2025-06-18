<?php

namespace App\Http\Controllers;

use stdClass;
use Throwable;
use SoapClient;
use App\Traits\TokenManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\contravel_bd\Agencia;
use App\Models\contravel_bd\Cliente;
use Illuminate\Support\Facades\Http;
use App\Models\tablero\Users_permiso;
use App\Models\tablero\Contravel_user;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ApiController;

class LoginController extends ApiController
{
    use TokenManage;

    protected $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
    }

    public function apiRoyal(Request $request)
    {
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
            // Mantener errors como array con mensajes
            return $this->errorResponse('Error de validación', $errors, 422);
        }

        $wsdl = 'https://agent.contravel.com.mx/AuthApi/Login';

        try {
            $response = Http::post($wsdl, [
                'agentusername' => $request->input('user'),
                'password' => $request->input('password'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info(json_encode($data));
                if (!empty($data) && isset($data['Status']) && $data['Status'] !== false) {
                    $user = new stdClass();
                    $user->id = $data['AgentId'];
                    $user->agency = $data['DkNumber'];
                    $user->agencyName = $data['AgencyName'];
                    $user->agencyMail = null;
                    $user->mail = null;
                    $user->name = $data['AgentFullName'];
                    $user->token = $data['Token'];
                    return $this->successResponse('Login Success', $user);
                } else {
                    return $this->errorResponse('Error al validar Usuario', $data, 404);
                }
            } else {
                $status = $response->status();
                $error = $response->json() ?? ['body' => $response->body()];
                return $this->errorResponse("Error en la solicitud", $error, $status);
            }
        } catch (Throwable $e) {
            return $this->errorResponse('Error en la solicitud',  $e->getMessage(), 500);
        }
    }

    private function apiIris(Request $request)
    {
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

        $wsdl = 'http://aereo.contravel.grupoiris.net/login-ws002/LoginService?wsdl';

        try {
            $client = new SoapClient($wsdl, [
                'trace' => true,
            ]);
            $params = [
                'Login' => $request->input('user'),
                'Password' => $request->input('password'),
            ];

            $response = $client->doLogin(['request' => $params]);
            $data = $response->response;
            Log::info(json_encode($data));

            $user = new stdClass();
            $user->id = $data->User->Id;
            $user->agency = $data->Agency->Reference;
            $user->agencyName = $data->Agency->Name;
            $user->agencyMail = $data->Agency->Email;
            $user->mail = $data->User->email;
            $user->name = trim($data->User->FirstName . ' ' . $data->User->LastName1 . ' ' . $data->User->LastName2);
            $user->token = $data->Uuid;

            return $this->successResponse('Login successful', $user);
        } catch (\SoapFault $e) {
            return $this->errorResponse('SOAP Fault',  $e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred', $e->getMessage(), 500);
        }
    }

    public function loginContravel(Request $request)
    {
        $api = self::apiRoyal($request)->getData(true);

        if (!$api['success']) {
            return $api;
        }

        if ($api['data']['agency'] !== "100100" && $api['data']['agency'] !== "030004") {
            return $this->errorResponse('Unauthorized', 'Sin autorización a plataformas.', 403);
        }

        $data = $api['data'];

        try {
            $key = mb_convert_encoding($this->secret, 'UTF-8');
            $cifrado = mb_convert_encoding($request->input('password'), 'UTF-8');
            $hash = hash_hmac('sha256', $cifrado, $key);

            DB::beginTransaction();

            $user = Contravel_user::updateOrCreate(
                ['id' => $data['id']],
                [
                    'user' => $request->input('user'),
                    'cifrado' => $hash,
                    'mail' => $data['mail'],
                    'full_name' => $data['name'],
                    'cve_agencia' => $data['agency'],
                ]
            );

            Users_permiso::updateOrCreate(
                [
                    'user' => $data['id'],
                    'permiso' => 3,
                ]
            );

            $jwt = $this->generateToken($user->id, $user->user, $data['token']);
            if (!$jwt->status) {
                DB::rollBack();
                return $this->errorResponse("Token Error",  $jwt->message, 500);
            }

            DB::commit();
            return $this->successResponse('Sesión iniciada correctamente',  $jwt->token);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error",  'No se pudo almacenar la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function loginAgencies(Request $request)
    {
        $api = self::apiIris($request)->getData(true);

        if (!$api['success']) {
            return $api;
        }

        if ($api['data']['agency'] !== "100100" && $api['data']['agency'] !== "030004") {
            return $this->errorResponse('Unauthorized', 'Sin autorización a plataformas.', 403);
        }

        $data = $api['data'];

        try {
            $key = mb_convert_encoding($this->secret, 'UTF-8');
            $cifrado = mb_convert_encoding($request->input('password'), 'UTF-8');
            $hash = hash_hmac('sha256', $cifrado, $key);

            DB::beginTransaction();

            $cliente = Cliente::updateOrCreate(
                ['id_iris' => $data['id']],
                [
                    'username' => $request->input('user'),
                    'cifrado' => $hash,
                    'full_name' => $data['name'],
                    'email' => $data['mail'],
                    'cve_agencia' => $data['agency'],
                ]
            );

            $agencia = Agencia::updateOrCreate(
                ['id_agencia' => $data['agency']],
                [
                    'Nombre_razonSo' => $data['agencyName'],
                    'email' => $data['agencyMail'],
                    'Acceso' => true,
                ]
            );

            $jwt = $this->generateToken($cliente->id_iris, $cliente->username, $data['token']);
            if (!$jwt->status) {
                DB::rollBack();
                return $this->errorResponse("Token Error",  $jwt->message, 500);
            }

            DB::commit();
            return $this->successResponse('Sesión iniciada correctamente',  $jwt->token);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'No se pudo almacenar la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }
}