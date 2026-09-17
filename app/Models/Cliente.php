<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'rut_empresa',
        'rubro',
        'razon_social',
        'telefono',
        'direccion',
        'nombre_contacto',
        'email_contacto',
    ];
}