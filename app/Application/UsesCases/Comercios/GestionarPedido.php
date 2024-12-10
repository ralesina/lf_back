<?php

namespace App\Application\UsesCases\Comercios;

use App\Domains\Clientes\Repositories\IPedidoRepository;
use App\Exceptions\DomainException;
use App\Exceptions\ValidationException;

class GestionarPedido
{
    private $pedidoRepository;

    public function __construct(IPedidoRepository $pedidoRepository)
    {
        $this->pedidoRepository = $pedidoRepository;
    }

    public function execute(int $idPedido, int $idComercio, string $nuevoEstado): bool
    {
        // Verificar si el pedido existe y pertenece al comercio
        $pedido = $this->pedidoRepository->findById($idPedido);

        if (!$pedido) {
            throw new DomainException('El pedido no existe.');
        }

        if ($pedido['id_comercio'] !== $idComercio) {
            throw new ValidationException('El comercio no tiene permiso para gestionar este pedido.');
        }

        // Validar estado permitido
        $estadosPermitidos = ['preparando', 'enviado', 'completado'];
        if (!in_array($nuevoEstado, $estadosPermitidos)) {
            throw new ValidationException('El estado especificado no es válido.');
        }

        // Actualizar el estado del pedido
        return $this->pedidoRepository->update($idPedido, ['estado' => $nuevoEstado]);
    }
}
