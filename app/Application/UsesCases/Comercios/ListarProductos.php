<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IProductoRepository;

class ListarProductos
{
    private $productoRepository;

    public function __construct(IProductoRepository $productoRepository)
    {
        $this->productoRepository = $productoRepository;
    }

    public function execute(int $idComercio): array
    {
        return $this->productoRepository->findByComercio($idComercio);
    }
}