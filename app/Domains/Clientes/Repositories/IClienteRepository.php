<?php
namespace App\Domains\Clientes\Repositories;

interface IClienteRepository
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function findByUsuario(int $idUsuario): ?array;
    public function create(array $data): array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
