<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargos extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'cargos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'precio',

    ];
    public $timestamps = false;
}