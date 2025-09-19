<?php

namespace App\Models\operadora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquetes extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'tbl_paquetes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'fecha_mod',
        'tipo',
        'titulo',
        'destino',
        'region',
        'fecha_expi',
        'duracion',
        'fechaselect',
        'salidas',
        'precio',
        'status',
        'multimedia',
        'imagen',
        'detalles',
        'pdf',
        'hotel',
        'avion',
        'traslado',
        'train',
        'alimentos',
        'crucero',
        'oferta',
        'nomProv',
        'nomOrig',
        'page',
        'nomComi',
    ];
    public $timestamps = false;
}
