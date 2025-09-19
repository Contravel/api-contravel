<?php

namespace App\Http\Controllers\Bitacora;

use Exception;
use Illuminate\Http\Request;
use App\Models\bitacora\Boletos;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BoletosController extends ApiController
{
    public function saveBoletos(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'idBitacora'   => 'required|integer',
            'listBoletos'  => 'required|array',
            'listBoletos.*.boleto' => 'required|string|max:255',
            'listBoletos.*.cargo'  => 'required|numeric|min:0',
            'listBoletos.*.precio' => 'required|numeric|min:0',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', $firstError, 422);
        }

        try {
            $idBitacora = $request->idBitacora;
            $listBoletos = $request->listBoletos;
            foreach ($listBoletos as $boletoData) {
                $cargoMasIva = round($boletoData['precio'] * 1.16);

                $boleto = new Boletos();
                $boleto->id_boleto   = $boletoData['boleto'];
                $boleto->id_bitacora = $idBitacora;
                $boleto->concepto    = $boletoData['cargo'];
                $boleto->cargo       = $cargoMasIva;
                $boleto->save();
            }

            return $this->successResponse('Boletos guardados correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar boletos',  $e->getMessage(), 500);
        }
    }

    public function deleteBoletoByBitacora(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', $firstError, 422);
        }

        try {
            $deleted = Boletos::where('id_bitacora', $request->id)->delete();

            if ($deleted === 0) {
                return $this->errorResponse(
                    'No se encontraron boletos para eliminar',
                    'No se encontraron boletos con ese ID de bitácora',
                    404
                );
            }

            return $this->successResponse('Boletos eliminados correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar boletos',  $e->getMessage(), 500);
        }
    }

    public function obtenerBoletos(Request $request)
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
            $boletos = Boletos::where('id_bitacora', $request->id)
                ->orderBy('id_boleto', 'desc')
                ->get();

            return $this->successResponse('Boletos obtenidos correctamente', $boletos);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los boletos',  $e->getMessage(), 500);
        }
    }

    public function eliminarBoleto(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', $firstError, 422);
        }

        try {
            $boleto = Boletos::where('id_boleto', $request->id)->first();
            Log::info($boleto);
            if (!$boleto) {
                return $this->errorResponse('Boleto no encontrado',  'No existe el boleto con el ID proporcionado', 404);
            }

            $boleto->delete();

            return $this->successResponse('Boleto eliminado correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el boleto',  $e->getMessage(), 500);
        }
    }

    public function saveBoleto(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id',
            'concepto',
            'cargo',
            'boleto',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = array_values($errors)[0][0] ?? 'Error desconocido';
            return $this->errorResponse('Error de validación', $firstError, 422);
        }

        try {
            $cargoMasIva = round($request->cargo * 1.16);

            $boleto = new Boletos();
            $boleto->id_boleto   = $request->boleto;
            $boleto->id_bitacora = $request->id;
            $boleto->concepto    = $request->concepto;
            $boleto->cargo       = $cargoMasIva;
            $boleto->save();

            return $this->successResponse('Boleto guardado correctamente');
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar el boleto',  $e->getMessage(), 500);
        }
    }
}
