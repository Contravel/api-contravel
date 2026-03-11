<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\admon_op\breakdownCar;
use App\Models\admon_op\catalogSuppliersCar;
use App\Models\Autos\AutosReservados;
use App\Models\Autos\ProveedoresAutos;
use App\Models\Autos\CataProvAuto;
use Illuminate\Http\Request;

class PostAutos extends Controller
{
    public function obtenerComisiones(Request $request)
    {
        $validateData = $request->validate([
            'id' => 'required', // Asegura que se proporcione una clave de reserva
        ]);
        $proveedorId = $validateData['id'];
        $comisiones = catalogSuppliersCar::where('id', $proveedorId)
            ->with(['comisiones.tipoComision'])
            ->first();

        if (!$comisiones) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $result = $comisiones->comisiones->map(function ($comision) {
            return [
                'id' => $comision->id,
                'comision' => $comision->comision,
                'tipo_destino' => $comision->tipoComision->tipo_destino,
                'tipo_comision' => $comision->tipoComision->tipo_comision,
                'tipo_pago' => $comision->tipoComision->tipo_pago,
            ];
        });

        return response()->json($result);
    }

    public function agregarProveedorAutos(Request $request)
    {
        try {
            $request->validate([
                'proveedor' => 'required',
                'tipo'      => 'required',
            ]);

            $proveedor = catalogSuppliersCar::create([
                'proveedor' => strtoupper($request->proveedor),
                'tipo'      => strtoupper($request->tipo),
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


    public function createDesgloseAutos(Request $request)
    {
        // Validar los datos de entrada (sin requerir que sean obligatorios)
        $request->validate([
            'agencia'         => 'nullable',
            'alcance'         => 'nullable',
            'arrendadora'     => 'nullable',
            'checkin'         => 'nullable',
            'checkout'        => 'nullable',
            'comiAgencia'     => 'nullable',
            'comisionable'    => 'nullable',
            'comprobante'     => 'nullable',
            'currency'        => 'nullable',
            'cxs'             => 'nullable',
            'destino'         => 'nullable',
            'dk'              => 'nullable',
            'email'           => 'nullable',
            'fchpago'         => 'nullable',
            'fecha'           => 'nullable',
            'fpago'           => 'nullable',
            'localizador'     => 'nullable',
            'mpago'           => 'nullable',
            'mventa'          => 'nullable',
            'netoAgencia'     => 'nullable',
            'noComPorc'       => 'nullable',
            'no_comisionable' => 'nullable',
            'noches'          => 'nullable',
            'obs'             => 'nullable',
            'operador'        => 'nullable',
            'porcomiagen'     => 'nullable',
            'preciop'         => 'nullable',
            'proveedor'       => 'nullable',
            'ref'             => 'nullable',
            'serie'           => 'nullable',
            'tipoCambio'      => 'nullable',
            'titular'         => 'nullable',
            'totalReserva'    => 'nullable',
        ]);

        try {
            // Crear el registro
            $insertData = breakdownCar::create([
                'agencia'         => $request->agencia,
                'alcance'         => $request->alcance,
                'arrendadora'     => $request->arrendadora,
                'checkin'         => $request->checkin,
                'checkout'        => $request->checkout,
                'comiAgencia'     => $request->comiAgencia,
                'comisionable'    => $request->comisionable,
                'comprobante'     => $request->comprobante,
                'currency'        => $request->currency,
                'cxs'             => $request->cxs,
                'destino'         => $request->destino,
                'dk'              => $request->dk,
                'email'           => $request->email,
                'fchpago'         => $request->fchpago,
                'fecha'           => $request->fecha,
                'fpago'           => $request->fpago,
                'localizador'     => $request->localizador,
                'mpago'           => $request->mpago,
                'mventa'          => $request->mventa,
                'netoAgencia'     => $request->netoAgencia,
                'noComPorc'       => $request->noComPorc,
                'no_comisionable' => $request->no_comisionable,
                'noches'          => $request->noches,
                'obs'             => $request->obs ?? 'No hay observaciones', // Establecer valor por defecto si está vacío
                'operador'        => $request->operador,
                'porcomiagen'     => $request->porcomiagen,
                'preciop'         => $request->preciop,
                'proveedor'       => $request->proveedor,
                'ref'             => $request->ref,
                'serie'           => $request->serie,
                'tipoCambio'      => $request->tipoCambio,
                'titular'         => $request->titular,
                'totalReserva'    => $request->totalReserva,
            ]);

            return response()->json(['Estatus' => 'true', 'message' => 'Datos insertados correctamente', 'data' => $insertData], 201);
        } catch (\Exception $e) {
            return response()->json(['Estatus' => 'false', 'message' => 'Error al insertar los datos: ' . $e->getMessage()], 400);
        }
    }

    public static function getProvCars()
    {
        // Obtener todos los registros de la tabla
        $proveedores = catalogSuppliersCar::all(); // Esto es equivalente a SELECT * FROM UserAcess

        // Retornar los datos en formato JSON
        return response()->json($proveedores);
    }
}
