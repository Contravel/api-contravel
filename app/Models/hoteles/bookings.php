<?php

namespace App\Models\hoteles;

use App\Models\admon_op\commentReservations;
use App\Models\admon_op\confirmedReservations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bookings extends Model
{
    protected $connection = 'mysql_hoteles'; // Especifica la conexión
    protected $table = 'hts_reservaciones';
    protected $primaryKey = 'CVE_RESERVACION';

    public $timestamps = false; // Desactivar timestamps
    public $incrementing = false; // Si tu clave primaria no es autoincrementable
    protected $keyType = 'string'; // Si la clave primaria no es un entero

    public function plataforma()
    {
        return $this->belongsTo(catalogPlataform::class, 'CVE_PLATAFORMA', 'cat_plataforma_id');
    }

    public function huesped()
    {
        return $this->hasOne(guestInformation::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }

    public function pagos()
    {
        return $this->hasOne(paymentHistory::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }
    public function canceladas()
    {
        return $this->hasOne(cancelledBooking::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }

    public function confirmadas()
    {
        return $this->hasOne(confirmedReservations::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }
    public function observaciones()
    {
        return $this->hasOne(commentReservations::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }
}
