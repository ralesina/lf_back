<?php


namespace App\Application\UsesCases\Comercios;

use App\Domains\Clientes\Repositories\IPedidoRepository;
use App\Domains\Comercios\Repositories\IComercioRepository;
use App\Exceptions\DomainException;
use App\Exceptions\ValidationException;

class ListarPedidosComercio
{
    private $pedidoRepository;
    private $comercioRepository;

    public function __construct(
        IPedidoRepository   $pedidoRepository,
        IComercioRepository $comercioRepository
    )
    {
        $this->pedidoRepository = $pedidoRepository;
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(int $idUsuario): array
    {
        // Verificar que el usuario tenga un comercio asociado
        $comercio = $this->comercioRepository->findByUsuario($idUsuario);

        if (!$comercio) {
            throw new ValidationException(['message' => 'Usuario no asociado a ningún comercio']);
        }

        return $this->pedidoRepository->findByComercio($comercio['id_comercio']);
    }
}