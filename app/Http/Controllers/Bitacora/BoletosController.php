<?php

namespace App\Http\Controllers\Bitacora;

use Exception;
use Illuminate\Http\Request;
use App\Models\bitacora\Boletos;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class BoletosController extends ApiController
{
    public function saveBoletos(Request $request)
    {
        // Validar estructura del request
        $validated = Validator::make($request->all(), [
            'idBitacora'   => 'required|integer',
            'listBoletos'  => 'required|array',
            'listBoletos.*.boleto' => 'required|string|max:255',
            'listBoletos.*.cargo'  => 'required|numeric|min:0',
            'listBoletos.*.precio' => 'required|numeric|min:0',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $idBitacora = $request->idBitacora;
            $listBoletos = $request->listBoletos;

            foreach ($listBoletos as $boletoData) {
                $cargoMasIva = round($boletoData['precio'] * 1.16);

                $boleto = new Boletos();
                $boleto->id_boleto  = $boletoData['boleto'];
                $boleto->id_bitacora = $idBitacora;
                $boleto->concepto   = $boletoData['cargo'];
                $boleto->cargo      = $cargoMasIva;
                $boleto->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Boletos guardados correctamente'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al guardar boletos', $e->getMessage(), 500);
        }
    }
    public function deleteBoletoByBitacora(Request $request)
    {
        // Validar el parámetro recibido
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            // Eliminar boletos con el id_bitacora proporcionado
            $deleted = Boletos::where('id_bitacora', $request->id)->delete();

            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron boletos para eliminar con ese ID de bitácora'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Boletos eliminados correctamente'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar boletos', $e->getMessage(), 500);
        }
    }

    public function obtenerBoletos(Request $request)
    {
        // Validación
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $boletos = Boletos::where('id_bitacora', $request->id)
                ->orderBy('id_boleto', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $boletos,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener los boletos', $e->getMessage(), 500);
        }
    }
    public function eliminarBoleto(Request $request)
    {
        // Validación del ID
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_boletos,id_boleto',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            $boleto = Boletos::where('id_boleto', $request->id)->first();

            if (!$boleto) {
                return $this->errorResponse('Boleto no encontrado', 'No existe el boleto con el ID proporcionado', 404);
            }

            $boleto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Boleto eliminado correctamente'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el boleto', $e->getMessage(), 500);
        }
    }
}