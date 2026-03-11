<?php

namespace App\Models\comission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservationAir extends Model
{
    protected $connection = 'mysql_comission';
    protected $table = 'reservas_aereas';

    protected $fillable = [
        'aerolineas',
        'boleto',
        'cla_fac',
        'clases',
        'clave_cliente',
        'comision_agencia_monto',
        'comision_agencia_pct',
        'comision_contravel_monto',
        'comision_contravel_pct',
        'factura',
        'fecha',
        'fecha_regreso',
        'fecha_salida',
        'forma_pago',
        'iva',
        'linea_aerea',
        'ndc',
        'nombre_cliente',
        'pasajero',
        'pnr',
        'ruta',
        'serie',
        'tarifa_mon_nal',
        'total',
        'tua',
        'utilidad_real',
        'vuelos'
    ];

    public $timestamps = false;
}
