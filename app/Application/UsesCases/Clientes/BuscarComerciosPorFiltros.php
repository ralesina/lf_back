<?php

namespace App\Application\UsesCases\Clientes;

use App\Domains\Comercios\Services\ComercioService;

class BuscarComerciosPorFiltros
{
    private $comercioService;

    public function __construct(ComercioService $comercioService)
    {
        $this->comercioService = $comercioService;
    }

    public function execute(array $filtros): array
    {
        return $this->comercioService->buscarPorFiltros($filtros);
    }
}