<?php

namespace App\Models\hoteles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cancelledBooking extends Model
{
    protected $connection = 'mysql_hoteles'; // Especifica la conexión
    protected $table = 'pltfrm_reservas_canceladas';
    protected $primaryKey = 'CVE_RESERVACION';
    public $timestamps = false; // Desactivar timestamps
    public $incrementing = false; // Si tu clave primaria no es autoincrementable
    protected $keyType = 'string'; // Si la clave primaria no es un entero
    // Permitir la asignación masiva para estos campos
    protected $fillable = [
        'CVE_RESERVACION',
        'CVE_USER'
    ];
}
