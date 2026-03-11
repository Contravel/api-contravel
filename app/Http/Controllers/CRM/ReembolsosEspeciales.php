<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Comisiones\ReembolsoEspecial;
use App\Models\comission\specialRefunds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReembolsosEspeciales extends Controller
{
  /**
   * GET /api/reembolsos-especiales
   * Obtener todos los registros ordenados por ID desc.
   */
  public function index()
  {
    try {
      $data = specialRefunds::orderBy('id', 'desc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Lista obtenida correctamente',
        'data' => $data
      ], 200);
    } catch (\Exception $e) {

      Log::error("Error en index ReembolsosEspeciales: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al obtener los registros'
      ], 500);
    }
  }

  /**
   * POST /api/reembolsos-especiales
   * Crear un nuevo registro.
   */
  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'dse' => 'required',
        'dk' => 'required',
        'nom_agencia' => 'required',
        'boleto' => 'required',
        'total' => 'required',
        'periodo' => 'required',
      ]);

      $item = specialRefunds::create($validated);

      return response()->json([
        'success' => true,
        'message' => 'Registro creado correctamente',
        'data' => $item
      ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      // Muestra en log qué campos llegan vacíos o incorrectos
      Log::error('Errores de validación ReembolsosEspeciales:', $e->errors());
      return response()->json([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {

      Log::error("Error en store ReembolsosEspeciales: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al crear el registro'
      ], 500);
    }
  }

  /**
   * PUT /api/reembolsos-especiales/{id}
   * Actualizar registro existente.
   */
  public function update(Request $request, $id)
  {
    try {
      $validated = $request->validate([
        'dse' => 'nullable|integer',
        'dk' => 'nullable|string|max:50',
        'nom_agencia' => 'nullable|string|max:50',
        'boleto' => 'required|string|max:50',
        'total' => 'required|string|max:50',
        'periodo' => 'required|string|max:50',
      ]);

      $item = specialRefunds::findOrFail($id);
      $item->update($validated);

      return response()->json([
        'success' => true,
        'message' => 'Registro actualizado correctamente',
        'data' => $item
      ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

      return response()->json([
        'success' => false,
        'message' => 'Registro no encontrado'
      ], 404);
    } catch (\Illuminate\Validation\ValidationException $e) {

      return response()->json([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {

      Log::error("Error en update ReembolsosEspeciales: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al actualizar el registro'
      ], 500);
    }
  }
}
