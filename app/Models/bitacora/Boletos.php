<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boletos extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'tbl_boletos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_boleto',
        'id_bitacora',
        'concepto',
        'cargo'
    ];
    public $timestamps = false;
}