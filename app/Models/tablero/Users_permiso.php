<?php

namespace App\Models\tablero;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_permiso extends Model
{
    protected $table = 'users_permisos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user',
        'permiso',

    ];


    public $timestamps = false;
}