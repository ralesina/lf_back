<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Exceptions\ValidationException;

class EditarInventario
{
    private $productoRepository;

    public function __construct(IProductoRepository $productoRepository)
    {
        $this->productoRepository = $productoRepository;
    }

    public function execute(int $idProducto, array $data): bool
    {
        if (!isset($data['stock']) || $data['stock'] < 0) {
            throw new ValidationException(['stock' => 'El stock debe ser mayor o igual a cero']);
        }

        return $this->productoRepository->update($idProducto, $data);
    }
}
