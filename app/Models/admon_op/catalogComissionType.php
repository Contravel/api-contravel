<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogComissionType extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'cata_tipo_comision';
    protected $fillable = ['tipo_destino', 'tipo_comision', 'tipo_pago'];
    public $timestamps = false;
}
