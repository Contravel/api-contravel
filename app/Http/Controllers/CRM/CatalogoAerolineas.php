<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\comission\airlinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CatalogoAerolineas extends Controller
{
  public function index()
  {
    try {
      $data = airlinesCatalog::orderBy('id', 'asc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Lista obtenida correctamente',
        'data' => $data
      ], 200);
    } catch (\Exception $e) {
      Log::error("Error en index CatalogoAerolineas: " . $e->getMessage());
      return response()->json(['success' => false], 500);
    }
  }

  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'code' => 'required|string|max:10',
        'nombre_aerolinea' => 'required|string|max:200',
        'nota' => 'nullable|string'
      ]);

      $item = airlinesCatalog::create($validated);

      return response()->json([
        'success' => true,
        'message' => 'Registro creado',
        'data' => $item
      ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $e->errors()
      ], 422);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $validated = $request->validate([
        'code' => 'required|string|max:10',
        'nombre_aerolinea' => 'required|string|max:200',
        'nota' => 'nullable|string'
      ]);

      $item = airlinesCatalog::findOrFail($id);
      $item->update($validated);

      return response()->json([
        'success' => true,
        'message' => 'Registro actualizado',
        'data' => $item
      ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
    }
  }

  public function destroy($id)
  {
    try {
      airlinesCatalog::destroy($id);

      return response()->json([
        'success' => true,
        'message' => 'Eliminado correctamente'
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['success' => false], 500);
    }
  }
}
