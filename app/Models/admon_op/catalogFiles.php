<?php

namespace App\Models\admon_op;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogFiles extends Model
{
    protected $connection = 'mysql_admon_op';

    // Especifica la tabla en la base de datos
    protected $table = 'archivos';

    // Define la clave primaria
    protected $primaryKey = 'id';

    // Desactiva los timestamps si no estás usando created_at y updated_at
    public $timestamps = false;

    // Laravel gestiona automáticamente el incremento de la clave primaria, por lo que no es necesario especificarlo
    // public $incrementing = true; // Opcional, ya que es el valor predeterminado

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre',
        'localizador',
    ];

    // Si los campos son de tipo datetime o similares, puedes definir su formato, pero no es necesario en tu caso.
    // protected $dates = ['created_at', 'updated_at'];
}
