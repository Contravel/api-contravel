<?php

namespace App\Http\Controllers\Tablero;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\tablero\Users_permiso;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class PermisosController extends ApiController
{
    public function obtenerPermisos(Request $request)
    {
        // Validar que el campo 'usuario' venga en la petición
        $validated = Validator::make($request->all(), [
            'usuario' => 'required|string|max:255',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            // Buscar los permisos del usuario
            $permisos = Users_permiso::where('user', $request->usuario)->pluck('permiso');

            return response()->json([
                'success' => true,
                'permisos' => $permisos
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener permisos', $e->getMessage(), 500);
        }
    }


    public function iris(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $client = new Client(['base_uri' => 'https://services.contravel.com.mx']);
            $options = [
                'form_params' => [
                    'user' => $request->usuario,
                    'password' => $request->password,
                ]
            ];
            $response = $client->post('/login/v1', $options);
            $datos = json_decode($response->getBody());

            if ($datos->status !== 'true') {
                return response()->json($datos);
            }

            // Sesión de usuario
            session([
                'usuario'    => $datos->userName,
                'firstName'  => $datos->firstName,
                'lastName'   => $datos->lastName1 . ' ' . $datos->lastName2,
                'email'      => $datos->email,
            ]);

            $this->consultarPermisoLocal($datos->userName);

            return response()->json(session()->all());
        } catch (Exception $e) {
            return $this->errorResponse('Error al autenticar con Iris', $e->getMessage(), 500);
        }
    }

    private function consultarPermisoLocal($user)
    {
        try {
            $permiso = Users_permiso::where('user', $user)
                ->where('permiso', 'USER_BITACORA')
                ->first();

            if (!$permiso) {
                return $this->asignarPermisoLocal($user);
            }

            return true;
        } catch (Exception $e) {
            return $this->errorResponse('Error al consultar permiso local', $e->getMessage(), 500);
        }
    }

    private function asignarPermisoLocal($user)
    {
        try {
            return Users_permiso::create([
                'user' => $user,
                'permiso' => 'USER_BITACORA',
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al asignar permiso local', $e->getMessage(), 500);
        }
    }

    public function consultarPermiso($user)
    {
        try {
            $permiso = Users_permiso::where('user', $user)->first();

            if (!$permiso) {
                return $this->asignarPermiso($user);
            }

            return true;
        } catch (Exception $e) {
            return $this->errorResponse('Error al consultar permiso', $e->getMessage(), 500);
        }
    }

    public function asignarPermiso($user)
    {
        try {
            return Users_permiso::create([
                'user' => $user,
                'permiso' => 'USER_GENERAL',
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al asignar permiso', $e->getMessage(), 500);
        }
    }
}