<?php

namespace App\Models\contravel_bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    protected $connection = 'mysql2';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_agencia',
        'Nombre_razonSo',
        'email',
        'Acceso',
    ];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(Cliente::class, 'id_agencia');
    }
}
