<?php

namespace App\Models\comission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class iataCatalog extends Model
{
    protected $connection = 'mysql_comission';
    protected $table = 'catalogo_iata';

    protected $fillable = [
        'code',
        'aeropuerto_internacional',
        'estado',
        'municipio',
        'pais',
    ];

    public $timestamps = false;
}
