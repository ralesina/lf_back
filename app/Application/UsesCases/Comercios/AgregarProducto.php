<?php
namespace App\Application\UsesCases\Comercios;

use App\Domains\Comercios\Services\ComercioService;
use App\Exceptions\ValidationException;

class AgregarProducto
{
    private $comercioService;

    public function __construct(ComercioService $comercioService)
    {
        $this->comercioService = $comercioService;
    }

    public function execute(array $data, $image = null): array
    {
        $this->validateData($data);
        return $this->comercioService->registrarProducto($data, $image);
    }

    private function validateData(array $data): void
    {
        $errors = [];

        if (empty($data['nombre_producto'])) {
            $errors['nombre_producto'] = 'El nombre del producto es requerido';
        }

        if (!isset($data['precio']) || $data['precio'] <= 0) {
            $errors['precio'] = 'El precio debe ser mayor que 0';
        }

        if (!isset($data['id_comercio'])) {
            $errors['id_comercio'] = 'El comercio es requerido';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}