<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Exceptions\DomainException;

class CambiarEstadoProducto
{
    private $productoRepository;

    public function __construct(IProductoRepository $productoRepository)
    {
        $this->productoRepository = $productoRepository;
    }

    public function execute(int $idProducto, string $estado): bool
    {
        if (!in_array($estado, ['activo', 'inactivo'])) {
            throw new DomainException('Estado no válido');
        }

        return $this->productoRepository->cambiarEstado($idProducto, $estado);
    }
}