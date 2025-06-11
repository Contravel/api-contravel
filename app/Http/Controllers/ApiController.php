<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    // ✅ Respuesta de éxito
    public function successResponse($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    // ❌ Respuesta de error
    public function errorResponse($message, $errors = [], $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

}