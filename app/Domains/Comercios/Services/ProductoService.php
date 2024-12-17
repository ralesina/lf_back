<?php
namespace App\Domains\Comercios\Services;

use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\DomainException;

class ProductoService
{
    private $productoRepository;

    public function __construct(IProductoRepository $productoRepository)
    {
        $this->productoRepository = $productoRepository;
    }

    public function actualizarProducto(int $idProducto, array $data): array
    {
        $this->validarDatosProducto($data);

        $producto = $this->productoRepository->findById($idProducto);
        if (!$producto) {
            throw new DomainException('Producto no encontrado');
        }

        return $this->productoRepository->update($idProducto, $data);
    }

    public function cambiarEstado(int $idProducto, string $estado): bool
    {
        $producto = $this->productoRepository->findById($idProducto);
        if (!$producto) {
            throw new DomainException('Producto no encontrado');
        }

        if (!in_array($estado, ['activo', 'inactivo'])) {
            throw new ValidationException(['estado' => 'Estado no válido']);
        }

        return $this->productoRepository->cambiarEstado($idProducto, $estado);
    }

    public function actualizarStock(int $idProducto, int $cantidad): bool
    {
        if ($cantidad < 0) {
            throw new ValidationException(['stock' => 'La cantidad no puede ser negativa']);
        }

        return $this->productoRepository->updateStock($idProducto, $cantidad);
    }
    public function listarProductosPorComercios(int $idComercio): array
    {
        if ($idComercio <= 0) {
            throw new \DomainException('El ID del comercio no es válido.');
        }

        $productos = $this->productoRepository->findByComercio($idComercio);

        if (empty($productos)) {
            throw new \DomainException('No se encontraron productos para este comercio.');
        }

        return $productos;
    }

    public function listarProductosPorCategoria(int $idCategoria, int $idComercio): array
    {
        if ($idCategoria <= 0 || $idComercio <= 0) {
            throw new \DomainException('IDs de comercio o categoría no válidos.');
        }

        return $this->productoRepository->findByCategoriaAndComercio($idCategoria, $idComercio);
    }

    public function getProductosComercio(int $idComercio): array
    {
        log_message('debug', 'Getting products for comercio: ' . $idComercio);

        $productos = $this->productoRepository->findByComercio($idComercio);

        log_message('debug', 'Found products: ' . json_encode($productos));

        return $productos;
    }
    private function validarDatosProducto(array $data): void
    {
        $errors = [];

        if (!empty($data['nombre_producto']) && strlen($data['nombre_producto']) < 3) {
            $errors['nombre_producto'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (isset($data['precio']) && $data['precio'] <= 0) {
            $errors['precio'] = 'El precio debe ser mayor que 0';
        }

        if (isset($data['stock']) && $data['stock'] < 0) {
            $errors['stock'] = 'El stock no puede ser negativo';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}