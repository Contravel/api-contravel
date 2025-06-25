<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReporteSeguimientosExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                $item->pnr,
                $item->id_boleto,
                $item->cve_agencia,
                $item->nombre_agencia,
                $item->servicio,
                $item->user,
                $item->descripcion,
                $item->concepto,
                $item->numCargo,
                $item->cargo,
                $item->fecha_registro,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'PNR',
            'ID BOLETO',
            'CLAVE AGENCIA',
            'NOMBRE AGENCIA',
            'SERVICIO',
            'USUARIO',
            'ESTATUS',
            'TIPO CARGO',
            'NO. CARGO',
            'CARGO',
            'FECHA DE COBRO',
        ];
    }
}
