<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $connection = 'mysql3';
    protected $primaryKey = 'id';
    protected $fillable = [
        'servicio',
    ];
    public $timestamps = false;
}
