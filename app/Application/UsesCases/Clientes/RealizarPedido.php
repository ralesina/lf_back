<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Services\PedidoService;
use App\Domains\Clientes\Entities\Pedido;
use App\Exceptions\ValidationException;

class RealizarPedido
{
    private $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function execute(array $data): array
    {
        return $this->pedidoService->crearPedido($data);
    }
}