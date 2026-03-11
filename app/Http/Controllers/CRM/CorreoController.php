<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Mail\EnviarCorreo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CorreoController extends Controller
{
    public function enviar(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'asunto' => 'required|string|max:255',
            'email1' => 'required|email',
            'email2' => 'nullable|email', // email2 es opcional
        ]);

        // Si la validación falla, retornamos un error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores en los datos proporcionados.',
                'errors' => $validator->errors(),
                'status' => 'false'
            ], 400);
        }

        // Preparar los detalles del correo
        $detalles = [
            'nombre' => $request->nombre,
            'url' => 'https://ejemplo.com/enlace-personalizado',
            'mensaje' => $request->mensaje,
            'asunto' => $request->asunto
        ];

        // Enviar el correo a los destinatarios proporcionados
        try {
            // Enviar a los correos proporcionados
            Mail::to([$request->email1, $request->email2])->send(new EnviarCorreo($detalles));

            return response()->json(['message' => 'Correo enviado correctamente', 'status' => 'true']);
        } catch (\Exception $e) {
            // En caso de error al enviar el correo
            return response()->json([
                'message' => 'Hubo un problema al enviar el correo.',
                'error' => $e->getMessage(),
                'status' => 'false'
            ], 500);
        }
    }
}
