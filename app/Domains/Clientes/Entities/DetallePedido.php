<?php
namespace App\Domains\Clientes\Entities;

use CodeIgniter\Entity\Entity;

class DetallePedido extends Entity
{
    protected $attributes = [
        'id_detalle' => null,
        'id_pedido' => null,
        'id_producto' => null,
        'cantidad' => null,
        'precio_unitario' => null,
        'subtotal' => null
    ];

    protected $casts = [
        'id_detalle' => 'integer',
        'id_pedido' => 'integer',
        'id_producto' => 'integer',
        'cantidad' => 'integer',
        'precio_unitario' => 'float',
        'subtotal' => 'float'
    ];
}
