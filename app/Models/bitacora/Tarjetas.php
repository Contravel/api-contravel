<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjetas extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'tbl_tarjetas';

    protected $primaryKey = 'id';
    protected $fillable = [
        'id_bitacora',
        'encrypt',
        'vencimiento',
        'cvv',
        'tipo_tarjeta'
    ];
    public $timestamps = false;
}