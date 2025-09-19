<?php

namespace App\Models\contravel_bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'mysql2';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_iris',
        'username',
        'cifrado',
        'full_name',
        'email',
        'cve_agencia'
    ];
    public $timestamps = false;

    public function agency()
    {
        return $this->belongsTo(Agencia::class, 'cve_agencia');
        // 👆 asegúrate que la FK se llame agency_id (o cámbiala aquí)
    }
}
