<?php

namespace App\Models\operadora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paises extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'tbl_paises';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pais',
        'region',
    ];
    public $timestamps = false;
}
