<?php
namespace App\Domains\Comercios\Entities;

use CodeIgniter\Entity\Entity;

class Producto extends Entity
{
    protected $attributes = [
        'id_producto' => null,
        'id_comercio' => null,
        'nombre_producto' => null,
        'descripcion' => null,
        'precio' => null,
        'categoria' => null,
        'imagen_url' => null,
        'stock' => 0,
        'estado' => 'activo',
        'fecha_creacion' => null,
        'id_categoria' => null
    ];

    protected $casts = [
        'id_producto' => 'integer',
        'id_comercio' => 'integer',
        'precio' => 'float',
        'stock' => 'integer',
        'id_categoria' => '?integer'
    ];

    protected const ESTADOS_VALIDOS = ['activo', 'inactivo'];

    public function esEstadoValido(string $estado): bool
    {
        return in_array($estado, self::ESTADOS_VALIDOS);
    }

    public function tieneStockSuficiente(int $cantidad): bool
    {
        return $this->attributes['stock'] >= $cantidad;
    }
}