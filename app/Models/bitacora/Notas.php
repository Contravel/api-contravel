<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notas extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'tbl_notas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_bitacora',
        'nota',
        'user'

    ];
    public $timestamps = false;
}