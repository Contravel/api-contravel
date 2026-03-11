<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CatalogoAerolineas\CatalogoIata;
use App\Models\comission\iataCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CatalogoIataController extends Controller
{
  /**
   * Obtener listado completo de códigos IATA
   */
  public function index()
  {
    try {
      $data = iataCatalog::orderBy('code', 'asc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Lista IATA obtenida correctamente',
        'data' => $data
      ], 200);
    } catch (\Exception $e) {
      Log::error("Error en index CatalogoIata: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error interno del servidor'
      ], 500);
    }
  }

  /**
   * Obtener un registro por código IATA
   */
  public function show($code)
  {
    try {
      $item = iataCatalog::where('code', $code)->firstOrFail();

      return response()->json([
        'success' => true,
        'message' => 'Registro encontrado',
        'data' => $item
      ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Código IATA no encontrado'
      ], 404);
    }
  }

  /**
   * Búsqueda opcional por filtros (estado, municipio, país)
   */
  public function search(Request $request)
  {
    try {
      $query = iataCatalog::query();

      if ($request->filled('estado')) {
        $query->where('estado', 'like', '%' . $request->estado . '%');
      }

      if ($request->filled('municipio')) {
        $query->where('municipio', 'like', '%' . $request->municipio . '%');
      }

      if ($request->filled('pais')) {
        $query->where('pais', 'like', '%' . $request->pais . '%');
      }

      $data = $query->orderBy('code', 'asc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Resultado de búsqueda',
        'data' => $data
      ], 200);
    } catch (\Exception $e) {
      Log::error("Error en search CatalogoIata: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error interno del servidor'
      ], 500);
    }
  }
}
