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
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', ['detalle' => $firstError], 422);
        }

        $idBitacora = $request->idBitacora;
        $nota = $request->nota ?? null;
        $estatus = $request->estatus;
        $historico = $request->historico;

        $saveNota = is_null($nota) || $nota === ''
            ? true
            : NotasController::guardarNotaDirecta($nota, $idBitacora);

        if (!$saveNota) {
            return $this->errorResponse('No se pudo guardar la nota', [], 400);
        }

        try {
            $seguimiento = Seguimientos::find($idBitacora);

            if (!$seguimiento) {
                return $this->errorResponse('Seguimiento no encontrado', ['detalle' => 'ID inválido'], 404);
            }

            $seguimiento->estatus = $estatus;

            if ($historico == 4) {
                $seguimiento->id_servicio = 2;
            }

            $seguimiento->save();

            return $this->successResponse('Estatus actualizado correctamente', []);
        } catch (Exception $e) {
            return $this->errorResponse('Error al actualizar el estatus', ['exception' => $e->getMessage()], 500);
        }
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
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', ['detalle' => $firstError], 422);
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

            return $this->successResponse('Bitácora creada correctamente', ['id_bitacora' => $idBitacora]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la bitácora', ['exception' => $e->getMessage()], 500);
        }
    }

    public function deleteBitacora(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', ['detalle' => $firstError], 422);
        }

        try {
            $bitacora = Seguimientos::find($request->id);

            if (!$bitacora) {
                return $this->errorResponse('Registro no encontrado', ['detalle' => 'ID inválido'], 404);
            }

            $bitacora->delete();

            return $this->successResponse('Registro eliminado correctamente', []);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el registro', ['exception' => $e->getMessage()], 500);
        }
    }

    public function saveCotizacionBitacora(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'pnr' => 'required|string',
            'cveAgencia' => 'required|string',
            'nomCliente' => 'required|string',
            'servicio' => 'required|integer',
            'cargo' => 'required|array|min:1',
            'cargo.0' => 'required|array',
            'cargo.0.boleto' => 'nullable',
            'status' => 'required|string',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', ['detalle' => $firstError], 422);
        }

        $user = auth()->user();

        try {
            $seguimiento = new Seguimientos();
            $seguimiento->pnr = $request->pnr;
            $seguimiento->cve_agencia = $request->cveAgencia;
            $seguimiento->nombre_agencia = $request->nomCliente;
            $seguimiento->user = $user->usuario ?? 'sistema';
            $seguimiento->id_servicio = $request->servicio;
            $seguimiento->estatus = $request->status;
            $seguimiento->save();

            $idBitacora = $seguimiento->id;

            $cargo = $request->cargo;
            $cargo[0]['boleto'] = now()->timestamp;

            $this->saveBoletos($cargo, $idBitacora);

            $nota = 'Se creó nueva Cotización para ' . $request->nomCliente;
            $this->saveNotas($nota, $idBitacora);

            return $this->successResponse('Cotización guardada correctamente', []);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la cotización', ['exception' => $e->getMessage()], 500);
        }
    }

    public function obtenerEstatus(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', ['detalle' => $firstError], 422);
        }

        try {
            $seguimiento = Seguimientos::select('estatus')->find($request->id);

            if (!$seguimiento) {
                return $this->errorResponse('Seguimiento no encontrado', ['detalle' => 'ID inválido'], 404);
            }

            return $this->successResponse('Estatus obtenido correctamente', ['estatus' => $seguimiento->estatus]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el estatus', ['exception' => $e->getMessage()], 500);
        }
    }

    public function obtenerBitacoras(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'user' => 'required|string',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', ['detalle' => $firstError], 422);
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

            return $this->successResponse('Bitácoras obtenidas correctamente', ['data' => $bitacoras]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener las bitácoras', ['exception' => $e->getMessage()], 500);
        }
    }
}