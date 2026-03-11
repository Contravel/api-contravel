<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogSuppliersCar extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'cata_prov_autos';
    protected $fillable = [
        'arrendadora',
        'proveedor',


    ];

    public $timestamps = false;
}
