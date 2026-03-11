<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\comission\AgreementsAir;
use App\Models\ConveniosAereos\ConvenioAereo;
use App\Services\CRM\ConveniosAereosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConveniosAereos extends Controller
{
  protected $service;

  public function __construct(ConveniosAereosService $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    try {
      $data = AgreementsAir::orderBy('orden', 'asc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Lista obtenida correctamente',
        'data' => $data
      ]);
    } catch (\Exception $e) {
      Log::error("Error index Convenios: " . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'Error interno'], 500);
    }
  }

  public function store(Request $request)
  {
    try {
      $orden = AgreementsAir::max('orden') + 1;

      $validated = $request->validate([
        'linea_aerea' => 'nullable|string',
        'clase' => 'nullable|string',
        'porcentaje_agen' => 'nullable|string',
        'porcentaje_cntrvl' => 'nullable|string',
        'p1_aplica_todos' => 'sometimes|string',
        'p2_sin_tocar_mex' => 'sometimes|string',
        'p3_frns_hacia_mex' => 'sometimes|string',
        'p4_inter_ogn_mex' => 'sometimes|string',
        'p5_comp_ogn_mex' => 'sometimes|string',
        'p6_ogn_usa_cad_excep_mex' => 'sometimes|string',
        'p7_ogn_mex' => 'sometimes|string',
      ]);

      $item = AgreementsAir::create([
        ...$validated,
        'orden' => $orden
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Registro creado correctamente',
        'data' => $item
      ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error validación',
        'errors' => $e->errors()
      ], 422);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $validated = $request->validate([
        'linea_aerea' => 'nullable|string',
        'clase' => 'nullable|string',
        'porcentaje_agen' => 'nullable|string',
        'porcentaje_cntrvl' => 'nullable|string',

        'p1_aplica_todos' => 'sometimes|string',
        'p2_sin_tocar_mex' => 'sometimes|string',
        'p3_frns_hacia_mex' => 'sometimes|string',
        'p4_inter_ogn_mex' => 'sometimes|string',
        'p5_comp_ogn_mex' => 'sometimes|string',
        'p6_ogn_usa_cad_excep_mex' => 'sometimes|string',
        'p7_ogn_mex' => 'sometimes|string',

        'orden' => 'sometimes|integer'
      ]);

      $item = AgreementsAir::findOrFail($id);
      $item->update($validated);

      return response()->json([
        'success' => true,
        'message' => 'Registro actualizado',
        'data' => $item
      ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
    }
  }

  public function destroy($id)
  {
    try {
      AgreementsAir::destroy($id);

      return response()->json([
        'success' => true,
        'message' => 'Eliminado'
      ]);
    } catch (\Exception $e) {
      Log::error("Error destroy Convenios: " . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'Error interno'], 500);
    }
  }

  public function reordenar(Request $request)
  {
    $orden = $request->input('orden');

    if (!is_array($orden)) {
      return response()->json([
        'success' => false,
        'message' => 'Formato inválido'
      ], 422);
    }

    foreach ($orden as $index => $id) {
      AgreementsAir::where('id', $id)
        ->update(['orden' => $index + 1]);
    }

    return response()->json([
      'success' => true,
      'message' => 'Orden actualizado correctamente'
    ]);
  }
}
