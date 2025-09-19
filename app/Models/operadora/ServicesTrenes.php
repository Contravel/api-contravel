<?php

namespace App\Models\operadora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicesTrenes extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'tbl_services_trenes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'ing',
        'img_prom',
        'video',
        'region'
    ];
    public $timestamps = false;
}
