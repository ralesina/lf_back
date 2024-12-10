<?php
namespace App\Domains\Comercios\Services;

use App\Domains\Comercios\Repositories\IComercioRepository;
use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Domains\Comercios\Entities\Comercio;
use App\Domains\Comercios\Entities\Producto;
use App\Exceptions\ValidationException;
use App\Exceptions\DomainException;

class ComercioService
{
    private $comercioRepository;
    private $productoRepository;

    public function __construct(IComercioRepository $comercioRepository, IProductoRepository $productoRepository)
    {
        $this->comercioRepository = $comercioRepository;
        $this->productoRepository = $productoRepository;
    }

    public function registrarComercio(array $data): Comercio
    {
        $this->validarDatosComercio($data);

        $comercio = new Comercio($data);
        if (!$comercio->validarUbicacion()) {
            throw new ValidationException(['ubicacion' => 'Ubicación geográfica inválida']);
        }

        return new Comercio($this->comercioRepository->create($data));
    }
    public function buscarCercano(float $latitud, float $longitud, float $radio): array
    {
        // Validar coordenadas y radio
        if ($latitud < -90 || $latitud > 90 || $longitud < -180 || $longitud > 180) {
            throw new ValidationException(['ubicacion' => 'Coordenadas geográficas inválidas']);
        }

        if ($radio <= 0) {
            throw new ValidationException(['radio' => 'El radio debe ser mayor que 0']);
        }

        // Delegar al repositorio la búsqueda
        return $this->comercioRepository->findNearby($latitud, $longitud, $radio);
    }
    public function registrarProducto(array $data, $image = null): array
    {
        if (!$this->comercioRepository->findById($data['id_comercio'])) {
            throw new DomainException('Comercio no encontrado');
        }

        if ($image) {
            $data['imagen_url'] = $this->uploadService->uploadImage($image, 'productos');
        }

        try {
            return $this->productoRepository->create($data);
        } catch (\Exception $e) {
            if ($data['imagen_url'] ?? false) {
                $this->uploadService->deleteImage($data['imagen_url']);
            }
            throw new DomainException('Error al registrar el producto: ' . $e->getMessage());
        }
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

        if (empty($data['categoria'])) {
            $errors['categoria'] = 'La categoría es requerida';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    private function validarDatosProducto(array $data): void
    {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre del producto es requerido';
        }

        if (empty($data['precio']) || $data['precio'] <= 0) {
            $errors['precio'] = 'El precio debe ser mayor que 0';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}