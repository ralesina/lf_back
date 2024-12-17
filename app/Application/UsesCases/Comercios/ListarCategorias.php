<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IComercioRepository;

class ListarCategorias
{
    private $comercioRepository;

    public function __construct(IComercioRepository $comercioRepository)
    {
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(): array
    {
        $categorias = $this->comercioRepository->getCategorias();
        return [
            'success' => true,
            'data' => $categorias
        ];
    }
}