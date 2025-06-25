<?php

namespace App\Models\bitacora;

use App\Models\bitacora\Boletos;
use App\Models\bitacora\SeguimientoCargos;
use App\Models\bitacora\Servicio;
use App\Models\bitacora\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimientos extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'tbl_seguimientos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'pnr',
        'cve_agencia',
        'nombre_agencia',
        'user',
        'id_servicio',
        'estatus',
    ];

    // Relaciones
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'estatus');
    }

    public function cargo()
    {
        return $this->hasOne(SeguimientoCargos::class, 'seguimiento');
    }
    public function boletos()
    {
        return $this->hasMany(Boletos::class, 'id_bitacora');
    }
}