<?php
namespace App\Domains\Comercios\Repositories;

interface IComercioRepository
{
    public function findById(int $id): ?array;
    public function findByUsuario(int $idUsuario): ?array;
    public function findNearby(float $latitud, float $longitud, float $radio): array;
    public function findByCategoria(string $categoria, ?array $filters = null): array;
    public function getDestacados(): array;
    public function getCategorias(): array;
    public function buscarPorFiltros(array $filters): array;
    public function create(array $data): array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
