<?php

namespace App\Models\comission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class specialRefunds extends Model
{
    protected $connection = 'mysql_comission';
    protected $table = 'reembolsos_especiales';

    protected $fillable = [
        'dse',
        'dk',
        'nom_agencia',
        'boleto',
        'total',
        'periodo'
    ];

    public $timestamps = false;

    protected $casts = [
        'modificado' => 'datetime',
    ];
}
