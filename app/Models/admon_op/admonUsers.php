<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admonUsers extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'admon_users';
    protected $fillable = ['cve_user', 'email', 'pre_valid', 'in_valid', 'post_valid'];
    public $timestamps = false;
}
