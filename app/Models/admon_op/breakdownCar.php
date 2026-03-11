<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class breakdownCar extends Model
{
    use HasFactory; // Asegúrate de usar este trait para la creación de modelos

    protected $connection = 'mysql_admon_op';
    protected $table = 'in_desgloses_cars';
    protected $primaryKey = 'CVE_RESERVACION';
    protected $fillable = [
        'agencia',
        'alcance',
        'arrendadora',
        'checkin',
        'checkout',
        'comiAgencia',
        'comisionable',
        'comprobante',
        'currency',
        'cxs',
        'destino',
        'dk',
        'email',
        'fchpago',
        'fecha',
        'fpago',
        'localizador',
        'mpago',
        'mventa',
        'netoAgencia',
        'noComPorc',
        'no_comisionable',
        'noches',
        'obs',
        'operador',
        'porcomiagen',
        'preciop',
        'proveedor',
        'ref',
        'serie',
        'tipoCambio',
        'titular',
        'totalReserva'
    ];

    public $timestamps = false; // Desactivar timestamps
    public $incrementing = false; // Si tu clave primaria no es autoincrementable
    protected $keyType = 'string'; // Si la clave primaria no es un entero
}
