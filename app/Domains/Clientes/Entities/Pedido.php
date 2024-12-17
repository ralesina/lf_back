<?php
namespace App\Domains\Clientes\Entities;

use CodeIgniter\Entity\Entity;

class Pedido extends Entity
{
    protected $attributes = [
        'id_pedido' => null,
        'id_cliente' => null,
        'id_comercio' => null,
        'direccion_entrega' => null,
        'telefono_contacto' => null,
        'total' => 0.00,
        'estado' => 'pendiente',
        'fecha_pedido' => null,
        'fecha_entrega' => null,
        'instrucciones' => null,
        'metodo_pago' => null
    ];

    protected $casts = [
        'id_pedido' => 'integer',
        'id_cliente' => 'integer',
        'id_comercio' => 'integer',
        'total' => 'float',
        'fecha_pedido' => 'datetime',
        'fecha_entrega' => 'datetime'
    ];

    public const ESTADOS_VALIDOS = [
        'pendiente',
        'confirmado',
        'en_preparacion',
        'en_camino',
        'entregado',
        'cancelado'
    ];

    public function cambiarEstado(string $nuevoEstado): bool
    {
        if (!in_array($nuevoEstado, self::ESTADOS_VALIDOS)) {
            return false;
        }

        $this->attributes['estado'] = $nuevoEstado;
        return true;
    }
}