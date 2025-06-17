<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgenciasAdmon extends Model
{
    protected $connection = 'mysql4';
    protected $primaryKey = 'AGENCIA_ID';
    protected $fillable = [
        'NUM_CLIENTE',
        'NOMBRE',
        'ESTATUS'
    ];
    public $timestamps = false;
}
