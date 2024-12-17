<?php

namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IComercioRepository;

class ListarComerciosDestacados
{
    private $comercioRepository;

    public function __construct(IComercioRepository $comercioRepository)
    {
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(): array
    {
        return $this->comercioRepository->getDestacados();
    }
}