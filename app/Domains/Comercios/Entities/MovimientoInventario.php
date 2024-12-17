<?php

namespace App\Domains\Comercios\Entities;

use CodeIgniter\Entity\Entity;

class MovimientoInventario extends Entity
{
    protected $attributes = [
        'id_movimiento' => null,
        'id_producto' => null,
        'cantidad' => null,
        'tipo' => null, // entrada/salida
        'motivo' => null,
        'fecha' => null
    ];

    protected $casts = [
        'id_movimiento' => 'integer',
        'id_producto' => 'integer',
        'cantidad' => 'integer'
    ];

    protected const TIPOS_VALIDOS = ['entrada', 'salida'];

    public function esTipoValido(string $tipo): bool
    {
        return in_array($tipo, self::TIPOS_VALIDOS);
    }
}