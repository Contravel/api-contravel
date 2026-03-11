<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class breakdownHotels extends Model
{
    use HasFactory; // Asegúrate de usar este trait para la creación de modelos

    protected $connection = 'mysql_admon_op';
    protected $table = 'in_desgloses';
    protected $primaryKey = 'CVE_RESERVACION';
    protected $fillable = [
        'CVE_RESERVACION',
        'USER',
        'proveedor',
        'CXS',
        'cve_agencia',
        'nom_agencia',
        'FCH_RESERVACION',
        'FCH_CHECKIN',
        'FCH_CHECKOUT',
        'FCH_PAGO',
        'NOMBRE_HOTEL',
        'TITULAR',
        'OBSERVACIONES',
        'TOTAL',
        'CURRENCY',
        'neto_proveedor',
        'COBRADO',
        'COMISION',
        'PORC_COMI',
        'COMISION_CLIENTE',
        'PORCENTAJE_CLIENTE',
        'DESTINO',
        'METODO_PAGO',
        'FORMA_PAGO',
        'alcance',
        'SERIE',
        'COMPROBANTE',
        'email',
        'REFAGEN',
        'NOCHES',
        'status',
        'servicio'

    ];

    public $timestamps = false; // Desactivar timestamps
    public $incrementing = false; // Si tu clave primaria no es autoincrementable
    protected $keyType = 'string'; // Si la clave primaria no es un entero

    public function canceladas()
    {
        return $this->hasOne(Canceladas::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }

    public function observaciones()
    {
        return $this->hasOne(commentReservations::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }

    public function confirmadas()
    {
        return $this->hasOne(confirmedReservations::class, 'CVE_RESERVACION', 'CVE_RESERVACION');
    }
}
