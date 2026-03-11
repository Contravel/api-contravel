<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\admon_op\breakdownTrains;
use App\Models\admon_op\catalogSupplierTrains;
use App\Models\Trenes\CataProvTrenes;
use App\Models\Trenes\TrenesReservados;
use Illuminate\Http\Request;


class PostTrenes extends Controller
{
    public function createDesgloseTrenes(Request $request)
    {
        // Validar los datos de entrada (sin requerir que sean obligatorios)
        $request->validate([
            'agencia'               => 'nullable',
            'agregarTPV'            => 'nullable',
            'alcance'               => 'nullable',
            'booking'               => 'nullable',
            'comiAgencia'           => 'nullable',
            'comisionable'          => 'nullable',
            'comprobante'           => 'nullable',
            'currency'              => 'nullable',
            'cxe'                   => 'nullable',
            'cxs'                   => 'nullable',
            'destino'               => 'nullable',
            'dk'                    => 'nullable',
            'email'                 => 'nullable',
            'fchpago'               => 'nullable',
            'fecha'                 => 'nullable',
            'fpago'                 => 'nullable',
            'localizador'           => 'nullable',
            'mpago'                 => 'nullable',
            'mventa'                => 'nullable',
            'netoTPV'               => 'nullable',
            'netop'                 => 'nullable',
            'noComPorc'             => 'nullable',
            'no_comisionable'       => 'nullable',
            'obs'                   => 'nullable',
            'operador'              => 'nullable',
            'porcomiagen'           => 'nullable',
            'preciop'               => 'nullable',
            'proveedor'             => 'nullable',
            'ref'                   => 'nullable',
            'serie'                 => 'nullable',
            'tipoCambio'            => 'nullable',
            'titular'               => 'nullable',
            'totalReserva'          => 'nullable',
        ]);


        try {
            // Crear el registro
            $insertData = breakdownTrains::create([
                'agencia'               => $request->agencia,
                'agregarTPV'            => $request->agregarTPV,
                'alcance'               => $request->alcance,
                'booking'               => $request->booking,
                'comiAgencia'           => $request->comiAgencia,
                'comisionable'          => $request->comisionable,
                'comprobante'           => $request->comprobante,
                'currency'              => $request->currency,
                'cxe'                   => $request->cxe,
                'cxs'                   => $request->cxs,
                'destino'               => $request->destino,
                'dk'                    => $request->dk,
                'email'                 => $request->email,
                'fchpago'               => $request->fchpago,
                'fecha'                 => $request->fecha,
                'fpago'                 => $request->fpago,
                'localizador'           => $request->localizador,
                'mpago'                 => $request->mpago,
                'mventa'                => $request->mventa,
                'netoTPV'               => $request->netoTPV,
                'netop'                 => $request->netop,
                'noComPorc'             => $request->noComPorc,
                'no_comisionable'       => $request->no_comisionable,
                'obs'                   => $request->obs ?? 'No hay observaciones', // Establecer valor por defecto si está vacío
                'operador'              => $request->operador,
                'porcomiagen'           => $request->porcomiagen,
                'preciop'               => $request->preciop,
                'proveedor'             => $request->proveedor,
                'ref'                   => $request->ref,
                'serie'                 => $request->serie,
                'tipoCambio'            => $request->tipoCambio,
                'titular'               => $request->titular,
                'totalReserva'          => $request->totalReserva,
            ]);


            return response()->json(['Estatus' => 'true', 'message' => 'Datos insertados correctamente', 'data' => $insertData], 201);
        } catch (\Exception $e) {
            return response()->json(['Estatus' => 'false', 'message' => 'Error al insertar los datos: ' . $e->getMessage()], 400);
        }
    }
    public static function getProveedoresTrenes()
    {
        try {

            $proveedores = catalogSupplierTrains::all();
            return response()->json($proveedores);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function agregarProveedorTrenes(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required',
            ]);

            $proveedor = catalogSupplierTrains::create([
                'nombre' =>
                $request->nombre,
            ]);

            return response()->json([
                'Estatus' => 'true',
                'message' => 'Datos insertados correctamente',
                'data'    => $proveedor,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'Estatus' => 'false',
                'message' => 'Error al insertar los datos: ' . $e->getMessage(),
            ], 400);
        }
    }
}
