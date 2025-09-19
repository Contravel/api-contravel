<?php

namespace App\Models\operadora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoDigital extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'catalogo_digital';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'image',
        'url'
    ];
    public $timestamps = false;
}
