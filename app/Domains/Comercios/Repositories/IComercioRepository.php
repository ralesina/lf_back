<?php
namespace App\Domains\Comercios\Repositories;

interface IComercioRepository
{
    public function findById(int $id): ?array;
    public function findNearby(float $latitud, float $longitud, float $radio): array;
    public function findByUsuario(int $idUsuario): ?array;

    public function create(array $data): array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}