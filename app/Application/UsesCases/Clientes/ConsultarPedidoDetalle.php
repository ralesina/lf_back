<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Services\PedidoService;

class ConsultarPedidoDetalle
{
    private $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function execute(int $idPedido, int $idCliente): array
    {
        return $this->pedidoService->getPedidoDetalle($idPedido, $idCliente);
    }
}