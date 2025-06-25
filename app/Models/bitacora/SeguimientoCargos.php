<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoCargos extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'seguimiento_cargo';
    protected $primaryKey = 'id';
    protected $fillable = ['seguimiento', 'numCargo', 'fecha_registro'];
    public $timestamps = false;
}
