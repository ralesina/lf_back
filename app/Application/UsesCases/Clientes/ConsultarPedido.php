<?php

namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Repositories\IClienteRepository;

class ConsultarPedido
{
    private $clienteRepository;

    public function __construct(IClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    public function execute(int $idCliente): array
    {
        return $this->clienteRepository->getPedidosCliente($idCliente);
    }
}
