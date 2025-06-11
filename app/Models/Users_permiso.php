<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_permiso extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'user',
        'permiso',

    ];
    public $timestamps = false;
}
