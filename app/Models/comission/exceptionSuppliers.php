<?php

namespace App\Models\comission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class exceptionSuppliers extends Model
{
    protected $connection = 'mysql_comission';
    protected $table = 'excepciones_proveedores';

    protected $fillable = [
        'code',
        'name'
    ];

    public $timestamps = false;
}
