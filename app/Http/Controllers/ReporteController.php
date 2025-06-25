<?php

namespace App\Http\Controllers;

use App\Exports\ReporteSeguimientosExport;
use Exception;
use Illuminate\Http\Request;
use App\Models\bitacora\Seguimientos;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends ApiController
{

    public function crearReporte(Request $request)
    {
        $validated = Validator::make($request->query(), [
            'inicio' => 'required|date',
            'final' => 'required|date',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }
        $startDate = Carbon::parse($request->inicio)->startOfDay();
        $endDate = Carbon::parse($request->final)->endOfDay();
        Log::debug('Fechas de inicio y fin del reporte', [
            'inicio' => $startDate,
            'final' => $endDate
        ]);
        try {
            // 1. Obtener los datos
            $data = Seguimientos::select(
                'tbl_seguimientos.pnr',
                'tbl_seguimientos.cve_agencia',
                'tbl_seguimientos.nombre_agencia',
                'tbl_seguimientos.user',
                'tbl_boletos.id_boleto',
                'tbl_boletos.cargo',
                'tbl_boletos.concepto',
                'servicios.servicio',
                'tbl_status.descripcion',
                'seguimiento_cargo.numCargo',
                'seguimiento_cargo.fecha_registro'
            )
                ->join('servicios', 'tbl_seguimientos.id_servicio', '=', 'servicios.id')
                ->join('tbl_status', 'tbl_seguimientos.estatus', '=', 'tbl_status.id')
                ->leftJoin('seguimiento_cargo', 'tbl_seguimientos.id', '=', 'seguimiento_cargo.seguimiento')
                ->leftJoin('tbl_boletos', 'tbl_seguimientos.id', '=', 'tbl_boletos.id_bitacora')
                ->whereBetween('seguimiento_cargo.fecha_registro', [$startDate, $endDate])
                ->get();
                Log::debug('Datos obtenidos para el reporte', ['data' => $data]);
            if ($data->isEmpty()) {
                return $this->errorResponse('Error al generar el reporte', 'No se encontraron resultados, no se genero reporte', 500);
            }

            $nombreArchivo = 'reporte_' . now()->format('Y_m_d_His') . '.xlsx';

            return Excel::download(new ReporteSeguimientosExport($data), $nombreArchivo);
        } catch (Exception $e) {
            return $this->errorResponse('Error al generar el reporte', $e->getMessage(), 500);
        }
    }
}
