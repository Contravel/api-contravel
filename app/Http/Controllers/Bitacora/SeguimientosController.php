<?php

namespace App\Http\Controllers\Bitacora;

use Exception;
use App\Traits\TokenManage;
use Illuminate\Http\Request;
use App\Models\bitacora\Seguimientos;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Bitacora\NotasController;

class SeguimientosController extends ApiController
{
    public function updateStatus(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'idBitacora' => 'required|integer',
            'nota' => 'required|string',
            'estatus' => 'required|string',
            'historico' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : 'Error desconocido';
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        $idBitacora = $validated['idBitacora'];
        $nota = $validated['nota'] ?? null;
        $estatus = $validated['estatus'];
        $historico = $validated['historico'];

        // Usar método estático para guardar nota
        $saveNota = is_null($nota) || $nota === '' ? true : NotasController::guardarNotaDirecta($nota, $idBitacora);

        if ($saveNota) {
            try {
                $seguimiento = Seguimientos::find($idBitacora);

                if (!$seguimiento) {
                    return response()->json(['success' => false, 'message' => 'Seguimiento no encontrado'], 404);
                }

                $seguimiento->estatus = $estatus;

                if ($historico == 4) {
                    $seguimiento->id_servicio = 2;
                }

                $seguimiento->save();

                return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente']);
            } catch (Exception $e) {
                return $this->errorResponse('Error al actualizar el estatus', $e->getMessage(), 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'No se pudo guardar la nota'], 400);
    }

    public function saveBitacora(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'pnr' => 'required|string|max:255',
            'cveAgencia' => 'required|string|max:255',
            'nomCliente' => 'required|string|max:255',
            'servicio' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $usuario = auth()->user();

            $seguimiento = new Seguimientos();
            $seguimiento->pnr = $request->pnr;
            $seguimiento->cve_agencia = $request->cveAgencia;
            $seguimiento->nombre_agencia = $request->nomCliente;
            $seguimiento->user = $usuario->usuario ?? 'sistema';
            $seguimiento->id_servicio = $request->servicio;
            $seguimiento->estatus = 1;
            $seguimiento->save();

            $idBitacora = $seguimiento->id;

            $nota = "Se creó nueva bitácora para " . $request->nomCliente;
            NotasController::guardarNotaDirecta($nota, $idBitacora);

            return response()->json([
                'success' => true,
                'message' => 'Bitácora creada correctamente',
                'id_bitacora' => $idBitacora
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la bitácora', $e->getMessage(), 500);
        }
    }
    public function deleteBitacora(Request $request)
    {
        // Validar que el ID sea un entero requerido
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $bitacora = Seguimientos::find($request->id);

            if (!$bitacora) {
                return response()->json(['success' => false, 'message' => 'Registro no encontrado'], 404);
            }

            $bitacora->delete();

            return response()->json(['success' => true, 'message' => 'Registro eliminado correctamente']);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el registro', $e->getMessage(), 500);
        }
    }

    public function saveCotizacionBitacora(Request $request)
    {
        // Validación
        $validated = Validator::make($request->all(), [
            'pnr' => 'required|string',
            'cveAgencia' => 'required|string',
            'nomCliente' => 'required|string',
            'servicio' => 'required|integer',
            'cargo' => 'required|array|min:1',
            'cargo.0' => 'required|array',
            'cargo.0.boleto' => 'nullable', // Se va a sobreescribir
            'status' => 'required|string',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        $user = auth()->user(); // O reemplázalo por tu sistema personalizado de sesión

        try {
            // Guardar seguimiento
            $seguimiento = new Seguimientos();
            $seguimiento->pnr = $request->pnr;
            $seguimiento->cve_agencia = $request->cveAgencia;
            $seguimiento->nombre_agencia = $request->nomCliente;
            $seguimiento->user = $user->usuario ?? 'sistema'; // Asegúrate que exista esta propiedad
            $seguimiento->id_servicio = $request->servicio;
            $seguimiento->estatus = $request->status;
            $seguimiento->save();

            $idBitacora = $seguimiento->id;

            // Asignar fecha actual al campo boleto del primer cargo
            $cargo = $request->cargo;
            $cargo[0]['boleto'] = now()->timestamp;

            // Guardar boletos
            $this->saveBoletos($cargo, $idBitacora);

            // Guardar nota
            $nota = 'Se creó nueva Cotización para ' . $request->nomCliente;
            $this->saveNotas($nota, $idBitacora);

            return response()->json([
                'success' => true,
                'message' => 'Cotización guardada correctamente',
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la cotización', $e->getMessage(), 500);
        }
    }
    public function obtenerEstatus(Request $request)
    {
        // Validar que el ID venga y sea un entero
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $seguimiento = Seguimientos::select('estatus')->find($request->id);

            if (!$seguimiento) {
                return $this->errorResponse('Seguimiento no encontrado', 'No se encontró un seguimiento con el ID proporcionado.', 404);
            }

            return response()->json([
                'success' => true,
                'estatus' => $seguimiento->estatus
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el estatus', $e->getMessage(), 500);
        }
    }
    public function obtenerBitacoras(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'user' => 'required|string',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        $user = $request->user;

        try {
            $admin = $this->validarAdmin($user);

            $query = Seguimientos::with([
                'servicio:id,servicio',
                'status:id,descripcion,color',
                'cargo:numCargo,seguimiento'
            ]);

            if (!$admin) {
                $query->where('user', $user);
            }

            $bitacoras = $query->get();

            return response()->json([
                'success' => true,
                'data' => $bitacoras
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener las bitácoras', $e->getMessage(), 500);
        }
    }
}