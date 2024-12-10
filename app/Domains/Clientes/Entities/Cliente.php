<?php

namespace App\Domains\Clientes\Entities;

use CodeIgniter\Entity\Entity;

class Cliente extends Entity
{
    protected $attributes = [
        'id_cliente' => null,
        'nombre' => null,
        'apellido' => null,
        'email' => null,
        'telefono' => null,
        'direccion' => null,
        'estado' => 'activo'
    ];

    protected $casts = [
        'id_cliente' => 'integer'
    ];
}

