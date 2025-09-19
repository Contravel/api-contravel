<?php

namespace App\Models\operadora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaContacto extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'tbl_visa_contacto';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'telefono',
        'archivo',
        'celular',
        'correo'
    ];
    public $timestamps = false;
}
