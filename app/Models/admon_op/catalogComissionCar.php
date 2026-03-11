<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogComissionCar extends Model
{
    protected $connection = 'mysql_admon_op';
    protected $table = 'cata_com_autos';
    protected $fillable = ['id_proveedor', 'data_comision', 'comision'];
    public $timestamps = false;
    public function tipoComision()
    {
        return $this->belongsTo(catalogComissionType::class, 'data_comision');
    }
}
