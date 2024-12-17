<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Services\PedidoService;

class ConsultarHistorialPedidos
{
    private $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function execute(int $idCliente): array
    {
        return $this->pedidoService->getHistorialPedidos($idCliente);
    }
}