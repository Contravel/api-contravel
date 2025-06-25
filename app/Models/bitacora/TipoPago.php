<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'tipo_tarjetas';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',

    ];
    public $timestamps = false;
}