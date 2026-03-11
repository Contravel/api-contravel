<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comissionTPV extends Model
{
    protected $connection = 'mysql_admon_op';


    protected $table = 'comision_tpv';
    protected $primaryKey = 'id';
    public $timestamps = false; // Desactivar timestamps


}
