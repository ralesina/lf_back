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
        'id_categoria' => null,
        'fecha_creacion' => null,
        'stock' => 0
    ];

    protected $casts = [
        'id_producto' => 'integer',
        'id_comercio' => 'integer',
        'precio' => 'float',
        'id_categoria' => '?integer',
        'stock' => 'integer'
    ];

    public function setPrecio($precio)
    {
        $this->attributes['precio'] = round((float)$precio, 2);
        return $this;
    }

    public function tieneStockSuficiente(int $cantidadRequerida): bool
    {
        return ($this->attributes['stock'] ?? 0) >= $cantidadRequerida;
    }
}