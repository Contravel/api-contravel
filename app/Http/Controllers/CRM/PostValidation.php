<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\admon_op\admonUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DesarrolloDB\PermisosUser;
use App\Models\DesarrolloDB\UserAcess;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PostValidation extends Controller
{
    public static function postLogout()
    {
        session()->flush();
        return response()->json(['message' => 'Sesión finalizada correctamente']);
    }

    public function postLogin(Request $request)
    {
        try {
            $validateData = $request->validate([
                'user' => 'required|string',
                'password' => 'required|string',
            ]);

            $usuario = $validateData['user'];
            $palabraSecreta = $validateData['password'];

            Log::info("Intento de login desde frontend", [
                'user' => $usuario,
                'ip' => $request->ip()
            ]);

            // Petición hacia IRIS (FORMATO REAL)
            $response = Http::asForm()->post('https://api.contravel.com.mx/api/login/v2', [
                'user' => $usuario,
                'password' => $palabraSecreta,
            ]);

            if ($response->failed()) {
                Log::error("Error al conectar con IRIS", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'message' => 'Error al conectar con el servicio externo',
                    'status' => false
                ], 500);
            }

            $datos = json_decode($response->body());

            /**********************************************
             * NUEVA VALIDACIÓN CORRECTA PARA IRIS v2
             **********************************************/
            if (
                isset($datos->success) &&
                $datos->success === true &&
                isset($datos->data)
            ) {
                // JWT devuelto por IRIS
                $jwt = $datos->data;

                // Parsear el JWT para obtener userName/email
                $payload = explode('.', $jwt)[1] ?? null;
                $payload = json_decode(base64_decode($payload));

                if (!$payload) {
                    return response()->json([
                        'message' => 'Token inválido recibido',
                        'status' => false
                    ], 400);
                }

                $userName = $payload->sub ?? $usuario;
                $email = $payload->email ?? null;

                // Guardar sesión local
                session([
                    "userName" => $userName,
                    "email" => $email
                ]);

                // Registrar usuario localmente
                $saved = $this->saveUser($userName, $email);

                return response()->json([
                    'status' => true,
                    'jwt' => $jwt,
                    'userName' => $userName,
                    'email' => $email,
                ], 200);
            }

            return response()->json([
                'message' => 'Credenciales inválidas',
                'status' => false,
            ], 401);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $e->errors(),
                'status' => false
            ], 400);
        } catch (\Exception $e) {
            Log::error("Error en postLogin: " . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor',
                'status' => false
            ], 500);
        }
    }

    public function saveUser($cve_user, $email)
    {
        try {
            $updatedUser = admonUsers::updateOrCreate(
                ['cve_user' => $cve_user],
                ['email' => $email]
            );

            return response()->json($updatedUser, 200);
        } catch (\Exception $e) {
            Log::error('Error al guardar el usuario: ' . $e->getMessage(), [
                'cve_user' => $cve_user,
                'email' => $email
            ]);

            return response()->json([
                'message' => 'Error interno del servidor al guardar el usuario'
            ], 500);
        }
    }
}
