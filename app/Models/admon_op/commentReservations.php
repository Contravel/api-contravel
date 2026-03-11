<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class commentReservations extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'post_reservas_observaciones';
    protected $primaryKey = 'CVE_RESERVACION';
    public $timestamps = false; // Desactivar timestamps
    public $incrementing = false; // Si tu clave primaria no es autoincrementable, también debes especificar esto

    protected $keyType = 'string'; // Si la clave primaria no es un entero

    // Permitir la asignación masiva para estos campos
    protected $fillable = [
        'CVE_RESERVACION',
        'OBSERVACIONES'
    ];
}
