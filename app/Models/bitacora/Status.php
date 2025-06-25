<?php

namespace App\Models\bitacora;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'mysql3';
    protected $table = 'tbl_status';
    protected $primaryKey = 'id';
    public $timestamps = false;
}