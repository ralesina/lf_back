<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Comercios\Services\ProductoService;
use App\Exceptions\ValidationException;

class ListarProductosComercio
{
    private $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function execute(int $id_comercio): array
    {
        $productos = $this->productoService->getProductosComercio($id_comercio);

        return [
            'success' => true,
            'data' => $productos  // Aquí productos ya es un array
        ];
    }
}