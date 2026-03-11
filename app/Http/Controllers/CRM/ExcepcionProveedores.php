<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\comission\exceptionSuppliers;
use App\Models\ProveedoresAereos\ExcepcionProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExcepcionProveedores extends Controller
{
  /**
   * GET /api/excepcion-proveedores
   */
  public function index()
  {
    try {

      $data = exceptionSuppliers::orderBy('id', 'asc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Lista obtenida correctamente',
        'data' => $data
      ], 200);
    } catch (\Exception $e) {

      Log::error("Error en index ExcepcionProveedores: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al obtener los registros'
      ], 500);
    }
  }

  /**
   * POST /api/excepcion-proveedores
   */
  public function store(Request $request)
  {
    try {

      $validated = $request->validate([
        'code' => 'required|integer',
        'name' => 'required|string'
      ]);

      $item = exceptionSuppliers::create($validated);

      return response()->json([
        'success' => true,
        'message' => 'Registro creado correctamente',
        'data' => $item
      ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {

      Log::error('Errores de validación ExcepcionProveedores:', $e->errors());

      return response()->json([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {

      Log::error("Error en store ExcepcionProveedores: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al crear el registro'
      ], 500);
    }
  }

  /**
   * PUT /api/excepcion-proveedores/{id}
   */
  public function update(Request $request, $id)
  {
    try {

      $validated = $request->validate([
        'code' => 'required|integer',
        'name' => 'required|string'
      ]);

      $item = exceptionSuppliers::findOrFail($id);
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

      Log::error("Error en update ExcepcionProveedores: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al actualizar el registro'
      ], 500);
    }
  }

  /**
   * DELETE /api/excepcion-proveedores/{id}
   */
  public function destroy($id)
  {
    try {

      exceptionSuppliers::destroy($id);

      return response()->json([
        'success' => true,
        'message' => 'Eliminado correctamente'
      ], 200);
    } catch (\Exception $e) {

      Log::error("Error en destroy ExcepcionProveedores: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al eliminar el registro'
      ], 500);
    }
  }
}
