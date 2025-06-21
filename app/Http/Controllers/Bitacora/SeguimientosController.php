<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Bitacora\NotasController;
use App\Http\Controllers\Bitacora\ResourceController;
use App\Models\bitacora\Seguimientos;
use App\Models\tablero\Users_permiso;
use App\Traits\TokenManage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeguimientosController extends ApiController
{
    public function updateStatus(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'idBitacora' => 'required',
            'nota',
            'estatus' => 'required',
            'historico',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        $idBitacora = $request->idBitacora;
        $nota = $request->nota ?? null;
        $estatus = $request->estatus;
        $historico = $request->historico;

        $saveNota = is_null($nota) || $nota === ''
            ? true
            : NotasController::guardarNotaDirecta($nota, $idBitacora, $request);

        if (!$saveNota) {
            return $this->errorResponse('No se pudo guardar la nota', "Error al guardar la nota", 400);
        }else if($estatus == 3){
            $saveNota = NotasController::guardarNotaDirecta("Bitacora Cancelada", $idBitacora, $request);
        }

        try {
            $seguimiento = Seguimientos::find($idBitacora);

            if (!$seguimiento) {
                return $this->errorResponse('Seguimiento no encontrado', 'ID inválido', 404);
            }

            $seguimiento->estatus = $estatus;

            if ($historico == 4) {
                $seguimiento->id_servicio = 2;
            }

            $seguimiento->save();

            return $this->successResponse('Estatus actualizado correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al actualizar el estatus',  $e->getMessage(), 500);
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
            return $this->errorResponse('Error de validación',  $firstError, 422);
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
            NotasController::guardarNotaDirecta($nota, $idBitacora, $request);

            return $this->successResponse('Bitácora creada correctamente',  $idBitacora);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar la bitácora',  $e->getMessage(), 500);
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
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $bitacora = Seguimientos::find($request->id);

            if (!$bitacora) {
                return $this->errorResponse('Registro no encontrado',  'ID inválid', 404);
            }

            $bitacora->delete();

            return $this->successResponse('Registro eliminado correctamente', []);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el registro',  $e->getMessage(), 500);
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
            return $this->errorResponse('Error de validación',  $firstError, 422);
        }

        try {
            $seguimiento = Seguimientos::select('estatus')->find($request->id);

            if (!$seguimiento) {
                return $this->errorResponse('Seguimiento no encontrado',  'ID inválid', 404);
            }

            return $this->successResponse('Estatus obtenido correctamente',  $seguimiento->estatus);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el estatus',  $e->getMessage(), 500);
        }
    }

    public function obtenerBitacoras(Request $request)
    {

        $resource = new ResourceController();
        $function = $resource->getUser($request)->getContent();
        $data = json_decode($function, true);
        $user = $data['data']['token']['id'];
        try {
            $admin = $this->validarAdmin($user);
            Log::debug($admin);
            $query = Seguimientos::with([
                'servicio:id,servicio',
                'status:id,descripcion,color',
                'cargo:numCargo,seguimiento'
            ]);

            if (!$admin) {
                $query->where('user', $user);
            }

            $bitacoras = $query->get();

            return $this->successResponse('Bitácoras obtenidas correctamente', $bitacoras);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener las bitácoras',  $e->getMessage(), 500);
        }
    }
    public function validarAdmin($user)
    {
        $admin = Users_permiso::where('user', $user)
            ->where('permiso', 3)
            ->exists();

        if ($admin) {
            return true;
        } else {
            return false;
        }
    }
}
