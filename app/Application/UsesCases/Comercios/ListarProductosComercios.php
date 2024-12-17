<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Domains\Comercios\Repositories\IComercioRepository;

class ListarProductosComercios
{
    private $productoRepository;
    private $comercioRepository;

    public function __construct(
        IProductoRepository $productoRepository,
        IComercioRepository $comercioRepository
    ) {
        $this->productoRepository = $productoRepository;
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(int $idUsuario): array
    {
        // Primero obtenemos el comercio asociado al usuario
        $comercio = $this->comercioRepository->findByUsuario($idUsuario);

        if (!$comercio) {
            throw new \RuntimeException('Usuario no asociado a ningún comercio');
        }

        $productos = $this->productoRepository->findByComercio($comercio['id_comercio']);

        return [
            'success' => true,
            'data' => $productos
        ];
    }
}