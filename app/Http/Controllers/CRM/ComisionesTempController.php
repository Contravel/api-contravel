<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComisionesTempController extends Controller
{
  public function uploadTemp(Request $request)
  {
    $rows = $request->input('rows');

    if (!$rows || !is_array($rows)) {
      return response()->json([
        'success' => false,
        'message' => 'Datos inválidos.'
      ], 400);
    }

    $tempId = uniqid('com_', true);

    Storage::disk('local')->put(
      "comisiones_temp/{$tempId}.json",
      json_encode($rows)
    );

    return response()->json([
      'success' => true,
      'temp_id' => $tempId,
    ]);
  }

  public function getTemp($id)
  {
    $path = "comisiones_temp/{$id}.json";

    if (!Storage::disk('local')->exists($path)) {
      return response()->json([
        'success' => false,
        'message' => 'Archivo no encontrado.'
      ], 404);
    }

    $data = json_decode(Storage::disk('local')->get($path), true);

    return response()->json([
      'success' => true,
      'data' => $data
    ]);
  }

  public function updateTemp(Request $request, $id)
  {
    $rows = $request->input('rows');

    if (!$rows || !is_array($rows)) {
      return response()->json([
        'success' => false,
        'message' => 'Datos inválidos.'
      ], 400);
    }

    $path = "comisiones_temp/{$id}.json";

    if (!Storage::disk('local')->exists($path)) {
      return response()->json([
        'success' => false,
        'message' => 'Archivo no encontrado.'
      ], 404);
    }

    Storage::disk('local')->put(
      $path,
      json_encode($rows)
    );

    return response()->json([
      'success' => true,
      'message' => 'Temp actualizado correctamente'
    ]);
  }

  /**
   * 🔥 Elimina archivo temporal
   */
  public function destroy($tempId)
  {
    $path = "comisiones_temp/{$tempId}.json";

    if (!Storage::disk('local')->exists($path)) {
      return response()->json([
        'success' => false,
        'message' => 'Archivo no encontrado.'
      ], 404);
    }

    Storage::disk('local')->delete($path);

    return response()->json([
      'success' => true,
      'message' => 'Archivo temporal eliminado correctamente.'
    ]);
  }
}
