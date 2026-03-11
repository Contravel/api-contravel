<?php

namespace App\Models\comission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class airlinesCatalog extends Model
{
    protected $connection = 'mysql_comission';
    protected $table = 'catalogo_aerolineas';

    protected $fillable = [
        'code',
        'nombre_aerolinea',
        'nota',
    ];

    public $timestamps = false;
}
