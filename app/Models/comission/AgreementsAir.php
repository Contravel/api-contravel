<?php

namespace App\Models\comission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementsAir extends Model
{
    protected $connection = 'mysql_comission';
    protected $table = 'convenios_aereos';

    protected $fillable = [
        'orden',
        'linea_aerea',
        'clase',
        'porcentaje_agen',
        'porcentaje_cntrvl',
        'p1_aplica_todos',
        'p2_sin_tocar_mex',
        'p3_frns_hacia_mex',
        'p4_inter_ogn_mex',
        'p5_comp_ogn_mex',
        'p6_ogn_usa_cad_excep_mex',
        'p7_ogn_mex'
    ];

    public $timestamps = false;
}
