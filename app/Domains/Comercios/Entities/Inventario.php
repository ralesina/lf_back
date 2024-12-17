<?php
namespace App\Domains\Comercios\Entities;

use CodeIgniter\Entity\Entity;

class Inventario extends Entity
{
    protected $attributes = [
        'id_inventario' => null,
        'id_producto' => null,
        'cantidad' => 0,
        'ultima_actualizacion' => null
    ];

    protected $casts = [
        'id_inventario' => 'integer',
        'id_producto' => 'integer',
        'cantidad' => 'integer'
    ];

    public function actualizarCantidad(int $cantidad): void
    {
        if ($cantidad < 0) {
            throw new \DomainException('La cantidad no puede ser negativa');
        }
        $this->attributes['cantidad'] = $cantidad;
        $this->attributes['ultima_actualizacion'] = date('Y-m-d H:i:s');
    }
}
