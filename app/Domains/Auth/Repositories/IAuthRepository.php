<?php
namespace App\Domains\Auth\Repositories;

interface IAuthRepository
{
    public function findByEmail(string $email);
    public function createUser(array $data);
    public function saveRefreshToken(int $userId, string $token);
    public function findById(int $userId);
    public function findComercioByUserId(int $userId);
    public function findClienteByUserId(int $userId);
    public function updateUser(int $userId, array $data): void;
    public function updateComercio(int $userId, array $data): void;
    public function updateCliente(int $userId, array $data): void;
}