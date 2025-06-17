<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\bitacora\Seguimientos;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class ReporteController extends Controller
{

    public function crearReporte(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'inicio' => 'required|date',
            'final' => 'required|date|after_or_equal:inicio',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors()->toArray();
            $firstError = is_array($errors) && count($errors) > 0 ? array_values($errors)[0] : ['Error desconocido'];
            return $this->errorResponse('Error de validación', $firstError[0], 422);
        }

        try {
            // 1. Obtener los datos
            $data = Seguimientos::with(['servicio', 'status', 'cargo', 'boletos'])
                ->whereHas('cargo', function ($query) use ($request) {
                    $query->whereBetween('fecha_registro', [$request->inicio, $request->final]);
                })
                ->get()
                ->map(function ($seguimiento) {
                    return [
                        'pnr' => $seguimiento->pnr,
                        'id_boleto' => optional($seguimiento->boletos->first())->id_boleto,
                        'cve_agencia' => $seguimiento->cve_agencia,
                        'nombre_agencia' => $seguimiento->nombre_agencia,
                        'servicio' => optional($seguimiento->servicio)->servicio,
                        'user' => $seguimiento->user,
                        'descripcion' => optional($seguimiento->status)->descripcion,
                        'concepto' => optional($seguimiento->boletos->first())->concepto,
                        'numCargo' => optional($seguimiento->cargo)->numCargo,
                        'cargo' => optional($seguimiento->boletos->first())->cargo,
                        'fecha_registro' => optional($seguimiento->cargo)->fecha_registro,
                    ];
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'error' => 1045,
                    'Desc' => 'No se puede generar Reporte, No se encontraron resultados'
                ]);
            }

            // 2. Generar archivo CSV
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Cabeceras
            $sheet->fromArray([
                ["PNR", "BOLETO", "CLAVE AGENCIA", "NOMBRE AGENCIA", "SERVICIO", "USUARIO", "ESTATUS", "TIPO CARGO", "NO. CARGO", "CARGO", "FECHA DE COBRO"]
            ], null, 'A1');

            // Datos
            $sheet->fromArray($data->toArray(), null, 'A2');

            $date_inicio = date('d-m-Y', strtotime($request->inicio));
            $date_final = date('d-m-Y', strtotime($request->final));
            $nombre = "REPORTE_" . $date_inicio . "_" . $date_final . ".csv";

            $writer = new Csv($spreadsheet);
            $writer->setUseBOM(true);
            $path = storage_path('app/public/reporte_csv/' . $nombre);

            // Crear carpeta si no existe
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $writer->save($path);

            return response()->json([
                'error' => 200,
                'Desc' => 'Reporte creado con éxito',
                'nombre' => $nombre,
                'url' => asset("storage/reporte_csv/$nombre")
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Error al generar el reporte', $e->getMessage(), 500);
        }
    }
}