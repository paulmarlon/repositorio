<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionInstituto extends Model
{
    protected $table = 'configuracion_institutos';

    protected $fillable = [
        'nombre_instituto',
        'logo',
        'direccion',
        'celular',
        'latitud',
        'longitud',
    ];
}
