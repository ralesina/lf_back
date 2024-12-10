<?php
namespace App\Domains\Auth\Services;

use App\Domains\Auth\Repositories\IAuthRepository;
use App\Domains\Auth\Entities\Usuario;
use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;

class AuthService
{
    private $repository;
    private $loginAttemptsLimit = 5;
    private $loginAttemptsWindow = 300; // 5 minutos

    public function __construct(IAuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function registerUser(array $data): array
    {
        $this->validateUserData($data);

        if ($this->repository->findByEmail($data['email'])) {
            throw new ValidationException(['email' => 'El email ya está registrado']);
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        unset($data['password']);

        return $this->repository->createUser($data);
    }

    public function authenticateUser(string $email, string $password): array
    {
        $user = $this->repository->findByEmail($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            throw new AuthenticationException('Credenciales inválidas');
        }

        $jwtService = new \App\Infrastructure\Auth\JWTService();
        $tokens = $jwtService->generateTokens($user->id_usuario, $user->rol);

        $this->repository->saveRefreshToken($user->id_usuario, $tokens['refresh_token']);

        return array_merge(
            ['user' => $user],
            $tokens
        );
    }

    private function validateUserData(array $data): void
    {
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        }

        if (empty($data['rol']) || !in_array($data['rol'], ['cliente', 'comercio'])) {
            $errors['rol'] = 'Rol inválido';
        }

        if ($data['rol'] === 'comercio') {
            if (empty($data['direccion'])) {
                $errors['direccion'] = 'La dirección es requerida para comercios';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    private function checkLoginAttempts(string $email): void
    {
        $attempts = $this->repository->getLoginAttempts($email, $this->loginAttemptsWindow);

        if ($attempts >= $this->loginAttemptsLimit) {
            throw new AuthenticationException('Demasiados intentos fallidos. Por favor, intente más tarde.');
        }
    }

    private function logLoginAttempt(string $email): void
    {
        $this->repository->logLoginAttempt([
            'email' => $email,
            'ip_address' => service('request')->getIPAddress()
        ]);
    }
}