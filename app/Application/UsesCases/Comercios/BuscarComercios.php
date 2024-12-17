<?php

namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IComercioRepository;
use App\Exceptions\ValidationException;

class BuscarComercios
{
    private $comercioRepository;

    public function __construct(IComercioRepository $comercioRepository)
    {
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(array $filters): array
    {
        return $this->comercioRepository->buscarPorFiltros($filters);
    }
}