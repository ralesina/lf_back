<?php
namespace App\Domains\Auth\Services;

use App\Domains\Auth\Repositories\IAuthRepository;
use App\Domains\Auth\Entities\Usuario;
use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Infrastructure\Auth\JWTService;

class AuthService
{
    private $repository;
    private $loginAttemptsLimit = 5;
    private $loginAttemptsWindow = 300; // 5 minutos
    private $jwtService;

    public function __construct(IAuthRepository $repository,         JWTService $jwtService
    )
    {
        $this->repository = $repository;
        $this->jwtService = $jwtService;

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

        $tokens = $this->jwtService->generateTokens($user->id_usuario, $user->rol);

        return [
            'success' => true,
            'data' => [
                'user' => [
                    'id_usuario' => $user->id_usuario,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'status' => $user->status
                ],
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => $tokens['expires_in']
            ],
            'message' => 'Login exitoso'
        ];
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
    public function getPerfil(int $userId): array
    {
        $usuario = $this->repository->findById($userId);
        if (!$usuario) {
            throw new \Exception('Usuario no encontrado');
        }

        $perfilData = [
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol
        ];

        // Si es comercio, obtener datos adicionales
        if ($usuario->rol === 'comercio') {
            $comercio = $this->repository->findComercioByUserId($userId);
            if ($comercio) {
                $perfilData = array_merge($perfilData, [
                    'direccion' => $comercio->direccion,
                    'telefono' => $comercio->telefono,
                    'latitud' => $comercio->latitud,
                    'longitud' => $comercio->longitud,
                    'radio_cercania' => $comercio->radio_cercania
                ]);
            }
        }
        // Si es cliente, obtener datos adicionales
        else if ($usuario->rol === 'cliente') {
            $cliente = $this->repository->findClienteByUserId($userId);
            if ($cliente) {
                $perfilData = array_merge($perfilData, [
                    'direccion' => $cliente->direccion,
                    'telefono' => $cliente->telefono
                ]);
            }
        }

        return [
            'success' => true,
            'data' => $perfilData
        ];
    }

    public function editarPerfil(int $userId, array $data): array
    {
        $usuario = $this->repository->findById($userId);
        if (!$usuario) {
            throw new \Exception('Usuario no encontrado');
        }

        // Validar datos
        $this->validateProfileData($data, $usuario->rol);

        // Actualizar usuario base
        $userData = [
            'nombre' => $data['nombre'],
            'email' => $data['email']
        ];

        $this->repository->updateUser($userId, $userData);

        // Actualizar datos específicos según rol
        if ($usuario->rol === 'comercio') {
            $comercioData = array_intersect_key($data, array_flip([
                'direccion', 'telefono', 'latitud', 'longitud', 'radio_cercania'
            ]));
            if (!empty($comercioData)) {
                $this->repository->updateComercio($userId, $comercioData);
            }
        }
        else if ($usuario->rol === 'cliente') {
            $clienteData = array_intersect_key($data, array_flip([
                'direccion', 'telefono'
            ]));
            if (!empty($clienteData)) {
                $this->repository->updateCliente($userId, $clienteData);
            }
        }

        return $this->getPerfil($userId);
    }

    private function validateProfileData(array $data, string $rol): void
    {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        }

        if ($rol === 'comercio') {
            if (empty($data['direccion'])) {
                $errors['direccion'] = 'La dirección es requerida para comercios';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}