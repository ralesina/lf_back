<?php
namespace App\Domains\Comercios\Services;

use App\Domains\Comercios\Repositories\IComercioRepository;
use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Infrastructure\Services\UploadService;
use App\Exceptions\ValidationException;
use App\Exceptions\DomainException;

class ComercioService
{
    private $comercioRepository;
    private $uploadService;

    public function __construct(
        IComercioRepository $comercioRepository,
        UploadService $uploadService
    ) {
        $this->comercioRepository = $comercioRepository;
        $this->uploadService = $uploadService;
    }

    public function registrarComercio(array $data): array
    {
        $this->validarDatosComercio($data);

        $comercio = new \App\Domains\Comercios\Entities\Comercio($data);
        if (!$comercio->validarUbicacion()) {
            throw new ValidationException(['ubicacion' => 'Ubicación geográfica inválida']);
        }

        return $this->comercioRepository->create($data);
    }

    public function registrarProducto(array $data, $image = null): array
    {
        // Procesar imagen si existe
        $imagenUrl = null;
        if ($image && $image->isValid()) {
            $imagenUrl = $this->uploadService->uploadImage($image, 'productos');
        }

        try {
            return $this->comercioRepository->registrarProducto($data, $imagenUrl);
        } catch (\Exception $e) {
            // Si algo falla y se subió una imagen, la eliminamos
            if ($imagenUrl) {
                $this->uploadService->deleteImage($imagenUrl);
            }
            throw $e;
        }
    }
    public function editarProducto(int $idProducto, array $data, ?array $file = null): bool
    {
        $editarProductoUseCase = service('editarProducto');
        return $editarProductoUseCase->execute($idProducto, $data, $file);
    }
    public function buscarCercano(float $latitud, float $longitud, float $radio): array
    {
        if ($latitud < -90 || $latitud > 90 || $longitud < -180 || $longitud > 180) {
            throw new ValidationException(['ubicacion' => 'Coordenadas geográficas inválidas']);
        }

        if ($radio <= 0) {
            throw new ValidationException(['radio' => 'El radio debe ser mayor que 0']);
        }

        return $this->comercioRepository->findNearby($latitud, $longitud, $radio);
    }

    public function getDestacados(): array
    {
        return $this->comercioRepository->getDestacados();
    }

    public function buscarPorFiltros(array $filtros): array
    {
        return $this->comercioRepository->buscarPorFiltros($filtros);
    }

    private function validarDatosComercio(array $data): void
    {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        }

        if (empty($data['direccion'])) {
            $errors['direccion'] = 'La dirección es requerida';
        }

        if (empty($data['latitud']) || empty($data['longitud'])) {
            $errors['ubicacion'] = 'La ubicación es requerida';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    private function validarDatosProducto(array $data): void
    {
        $errors = [];

        if (empty($data['nombre_producto'])) {
            $errors['nombre_producto'] = 'El nombre del producto es requerido';
        }

        if (empty($data['precio']) || $data['precio'] <= 0) {
            $errors['precio'] = 'El precio debe ser mayor que 0';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}