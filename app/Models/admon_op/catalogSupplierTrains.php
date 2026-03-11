<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogSupplierTrains extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'cata_prov_trenes';
    protected $fillable = ['nombre'];
    public $timestamps = false;
}
