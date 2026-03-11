<?php

namespace App\Models\hoteles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogPlataform extends Model
{
    protected $connection = 'mysql_hoteles';
    protected $table = 'tbl_cat_plataforma';
}
