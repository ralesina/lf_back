<?php

namespace App\Application\UsesCases\Comercios;

use App\Infrastructure\Services\UploadService;
use App\Domains\Comercios\Repositories\IProductoRepository;

class EditarProducto
{
    private IProductoRepository $productoRepository;
    private UploadService $uploadService;

    public function __construct(IProductoRepository $productoRepository, UploadService $uploadService)
    {
        $this->productoRepository = $productoRepository;
        $this->uploadService = $uploadService;
    }

    public function execute(int $idProducto, array $data, $imagen = null): array
    {
        // Validar datos básicos
        if (empty($data['nombre_producto']) || !is_string($data['nombre_producto'])) {
            throw new \InvalidArgumentException('El nombre del producto es obligatorio');
        }

        if (empty($data['precio']) || !is_numeric($data['precio']) || $data['precio'] <= 0) {
            throw new \InvalidArgumentException('El precio es obligatorio y debe ser mayor a 0');
        }

        // Si hay imagen nueva, procesarla
        if ($imagen && $imagen->isValid()) {
            $uploadResult = $this->uploadService->uploadImage($imagen, 'productos');
            $data['imagen_url'] = $uploadResult;
        }

        // Actualizar en base de datos
        $updated = $this->productoRepository->update($idProducto, $data);

        if (!$updated) {
            throw new \RuntimeException('Error al actualizar el producto');
        }

        return [
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'data' => $data
        ];
    }
}