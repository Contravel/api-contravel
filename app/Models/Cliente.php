<?php

namespace App\Models;

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
}
