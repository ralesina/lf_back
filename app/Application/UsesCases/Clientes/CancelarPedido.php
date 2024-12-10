<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Repositories\IPedidoRepository;
use App\Exceptions\DomainException;
use App\Exceptions\ValidationException;

class CancelarPedido
{
    private $pedidoRepository;

    public function __construct(IPedidoRepository $pedidoRepository)
    {
        $this->pedidoRepository = $pedidoRepository;
    }

    public function execute(int $idPedido, int $idCliente): bool
    {
        // Verificar si el pedido existe y pertenece al cliente
        $pedido = $this->pedidoRepository->findById($idPedido);

        if (!$pedido) {
            throw new DomainException('El pedido no existe.');
        }

        if ($pedido['id_cliente'] !== $idCliente) {
            throw new ValidationException('El cliente no tiene permiso para cancelar este pedido.');
        }

        if ($pedido['estado'] !== 'pendiente') {
            throw new DomainException('Solo se pueden cancelar pedidos en estado pendiente.');
        }

        // Actualizar el estado del pedido a "cancelado"
        return $this->pedidoRepository->update($idPedido, ['estado' => 'cancelado']);
    }
}
