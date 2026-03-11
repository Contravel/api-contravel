<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\comission\reservationAir;
use App\Models\ReservasAereas\ReservasAereo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReservasAereasController extends Controller
{
  // GET /api/reservas-aereas
  public function index()
  {
    try {
      $data = reservationAir::orderBy('id', 'asc')->get();

      return response()->json([
        'success' => true,
        'message' => 'Lista de reservas obtenida correctamente',
        'data' => $data
      ], 200);
    } catch (\Exception $e) {
      Log::error("Error en index ReservasAereas: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'Error al obtener las reservas'
      ], 500);
    }
  }

  // POST /api/reservas-aereas
  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'aerolineas' => 'nullable|string',
        'boleto' => 'nullable|numeric',
        'cla_fac' => 'nullable|string',
        'clases' => 'nullable|string',
        'clave_cliente' => 'nullable|string',
        'comision_agencia_monto' => 'nullable|numeric',
        'comision_agencia_pct' => 'nullable|numeric',
        'comision_contravel_monto' => 'nullable|numeric',
        'comision_contravel_pct' => 'nullable|numeric',
        'factura' => 'nullable|numeric',
        'fecha' => 'nullable|date_format:Y-m-d',
        'fecha_regreso' => 'nullable|date_format:Y-m-d',
        'fecha_salida' => 'nullable|date_format:Y-m-d',
        'forma_pago' => 'nullable|string',
        'iva' => 'nullable|numeric',
        'linea_aerea' => 'nullable|string',
        'ndc' => 'nullable|boolean',
        'nombre_cliente' => 'nullable|string',
        'pasajero' => 'nullable|string',
        'pnr' => 'nullable|string',
        'ruta' => 'nullable|string',
        'serie' => 'nullable|string',
        'tarifa_mon_nal' => 'nullable|numeric',
        'total' => 'nullable|numeric',
        'tua' => 'nullable|numeric',
        'utilidad_real' => 'nullable|numeric',
        'vuelos' => 'nullable|string'
      ]);

      $reserva = reservationAir::create($validated);

      return response()->json([
        'success' => true,
        'message' => 'Reserva creada correctamente',
        'data' => $reserva
      ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      Log::error('Errores de validación ReservasAereas:', $e->errors());
      return response()->json([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {
      Log::error("Error en store ReservasAereas: " . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Error al crear la reserva'
      ], 500);
    }
  }

  // PUT /api/reservas-aereas/{id}
  public function update(Request $request, $id)
  {
    try {
      $validated = $request->validate([
        'aerolineas' => 'nullable|string',
        'boleto' => 'nullable|numeric',
        'cla_fac' => 'nullable|string',
        'clases' => 'nullable|string',
        'clave_cliente' => 'nullable|string',
        'comision_agencia_monto' => 'nullable|numeric',
        'comision_agencia_pct' => 'nullable|numeric',
        'comision_contravel_monto' => 'nullable|numeric',
        'comision_contravel_pct' => 'nullable|numeric',
        'factura' => 'nullable|numeric',
        'fecha' => 'nullable|date_format:Y-m-d',
        'fecha_regreso' => 'nullable|date_format:Y-m-d',
        'fecha_salida' => 'nullable|date_format:Y-m-d',
        'forma_pago' => 'nullable|string',
        'iva' => 'nullable|numeric',
        'linea_aerea' => 'nullable|string',
        'ndc' => 'nullable|boolean',
        'nombre_cliente' => 'nullable|string',
        'pasajero' => 'nullable|string',
        'pnr' => 'nullable|string',
        'ruta' => 'nullable|string',
        'serie' => 'nullable|string',
        'tarifa_mon_nal' => 'nullable|numeric',
        'total' => 'nullable|numeric',
        'tua' => 'nullable|numeric',
        'utilidad_real' => 'nullable|numeric',
        'vuelos' => 'nullable|string'
      ]);

      $reserva = reservationAir::findOrFail($id);
      $reserva->update($validated);

      return response()->json([
        'success' => true,
        'message' => 'Reserva actualizada correctamente',
        'data' => $reserva
      ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Reserva no encontrada'
      ], 404);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {
      Log::error("Error en update ReservasAereas: " . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Error al actualizar la reserva'
      ], 500);
    }
  }

  // DELETE /api/reservas-aereas/{id}
  public function destroy($id)
  {
    try {
      reservationAir::destroy($id);

      return response()->json([
        'success' => true,
        'message' => 'Reserva eliminada correctamente'
      ], 200);
    } catch (\Exception $e) {
      Log::error("Error en destroy ReservasAereas: " . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Error al eliminar la reserva'
      ], 500);
    }
  }
}
