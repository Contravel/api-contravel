<?php

namespace App\Models\tablero;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agencies extends Model
{
    protected $table = 'contravel_agencias';
    protected $primaryKey = 'cve_agencia';
    protected $fillable = [
        'nombre_agencia',
        'correo_agencia',
    ];
    public $timestamps = false;

        public function users()
    {
        return $this->hasMany(Contravel_user::class, 'cve_agencia');
    }
}
