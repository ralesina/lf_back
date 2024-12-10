<?php
namespace App\Domains\Auth\Repositories;

interface IAuthRepository
{
    public function findByEmail(string $email);
    public function createUser(array $data);
    public function saveRefreshToken(int $userId, string $token);
}