<?php
namespace App\Domains\Comercios\Entities;

use CodeIgniter\Entity\Entity;

class Comercio extends Entity
{
    protected $attributes = [
        'id_comercio' => null,
        'id_usuario' => null,
        'nombre' => null,
        'direccion' => null,
        'latitud' => null,
        'longitud' => null,
        'radio_cercania' => null,
        'telefono' => null,
        'email' => null,
        'categoria' => null,
        'horario' => null,
        'estado' => 'activo'
    ];

    protected $casts = [
        'id_comercio' => 'integer',
        'id_usuario' => 'integer',
        'latitud' => 'double',
        'longitud' => 'double',
        'radio_cercania' => 'integer'
    ];

    public function validarUbicacion(): bool
    {
        return !empty($this->attributes['latitud'])
            && !empty($this->attributes['longitud'])
            && $this->attributes['latitud'] >= -90
            && $this->attributes['latitud'] <= 90
            && $this->attributes['longitud'] >= -180
            && $this->attributes['longitud'] <= 180;
    }
}