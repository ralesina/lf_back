<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Services\ComercioService;

class BuscarCercano
{
    private $comercioService;

    public function __construct(ComercioService $comercioService)
    {
        $this->comercioService = $comercioService;
    }

    public function execute(float $latitud, float $longitud, int $radio): array
    {
        return $this->comercioService->buscarCercano($latitud, $longitud, $radio);
    }
}