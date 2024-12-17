<?php
namespace App\Domains\Comercios\Repositories;

interface IProductoRepository
{
    public function findById(int $id): ?array;
    public function findByComercio(int $idComercio): array;
    public function findByCategoriaAndComercio(int $idCategoria, int $idComercio): array;
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function updateStock(int $id, int $cantidad): bool;
    public function cambiarEstado(int $id, string $estado): bool;
    public function delete(int $id): bool;
    public function existsInComercio(int $idProducto, int $idComercio): bool;
}