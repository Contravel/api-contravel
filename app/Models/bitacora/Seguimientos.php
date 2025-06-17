<?php

namespace App\Models\bitacora;

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
        return $this->belongsTo(\App\Models\bitacora\Servicio::class, 'id_servicio');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\bitacora\Status::class, 'estatus');
    }

    public function cargo()
    {
        return $this->hasOne(\App\Models\bitacora\SeguimientoCargos::class, 'seguimiento');
    }
    public function boletos()
    {
        return $this->hasMany(\App\Models\bitacora\Boletos::class, 'id_bitacora');
    }
}