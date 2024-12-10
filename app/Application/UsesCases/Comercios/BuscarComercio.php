<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IComercioRepository;

class BuscarComercio
{
    private $comercioRepository;

    public function __construct(IComercioRepository $comercioRepository)
    {
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(int $id): ?array
    {
        return $this->comercioRepository->findById($id);
    }
}