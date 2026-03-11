<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogSupplierHotels extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'cata_prov_hoteles';
    protected $fillable = [
        'nombre',

    ];

    public $timestamps = false;
}
