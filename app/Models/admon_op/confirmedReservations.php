<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class confirmedReservations extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'post_reservas_confirmadas';

    // Permitir la asignación masiva para estos campos
    protected $fillable = [
        'NOM_OPERADOR',
        'OBSERVACIONES',
        'CVE_RESERVACION',
        'CVE_USER'
    ];

    // Desactivar el manejo de timestamps
    public $timestamps = false;
}
