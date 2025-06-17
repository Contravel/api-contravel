<?php

namespace App\Models\tablero;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contravel_user extends Model
{
    protected $fillable = [
        'id',
        'user',
        'cifrado',
        'mail',
        'full_name',
        'cve_agencia',
    ];
    public $timestamps = false;
}