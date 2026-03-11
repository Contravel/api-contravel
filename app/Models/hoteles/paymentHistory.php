<?php

namespace App\Models\hoteles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymentHistory extends Model
{
    protected $connection = 'mysql_hoteles';
    protected $table = 'hts_pagos';
}
