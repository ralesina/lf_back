<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Services\PedidoService;

class ConsultarPedidosActivos
{
    private $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function execute(int $idCliente): array
    {
        return $this->pedidoService->getPedidosActivos($idCliente);
    }
}