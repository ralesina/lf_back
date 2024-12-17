<?php
namespace App\Application\UsesCases\Comercios;
use App\Domains\Comercios\Services\ComercioService;
use App\Domains\Comercios\Repositories\IComercioRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\DomainException;

class AgregarProducto
{
    private $comercioService;
    private $comercioRepository;

    public function __construct(
        ComercioService $comercioService,
        IComercioRepository $comercioRepository
    ) {
        $this->comercioService = $comercioService;
        $this->comercioRepository = $comercioRepository;
    }

    public function execute(array $data, $image = null): array
    {
        $this->validateData($data);

        // Obtener el comercio asociado al usuario
        $comercio = $this->comercioRepository->findByUsuario($data['id_usuario']);
        if (!$comercio) {
            throw new DomainException('Usuario no asociado a ningún comercio');
        }

        // Agregar el id_comercio a los datos
        $data['id_comercio'] = $comercio['id_comercio'];

        return $this->comercioService->registrarProducto($data, $image);
    }

    private function validateData(array $data): void
    {
        $errors = [];

        if (empty($data['id_usuario'])) {
            $errors['id_usuario'] = 'El ID de usuario es requerido';
        }

        if (empty($data['nombre_producto'])) {
            $errors['nombre_producto'] = 'El nombre del producto es requerido';
        }

        if (!isset($data['precio']) || $data['precio'] <= 0) {
            $errors['precio'] = 'El precio debe ser mayor que 0';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}