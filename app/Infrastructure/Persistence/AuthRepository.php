<?php
namespace App\Infrastructure\Persistence;

use CodeIgniter\Database\ConnectionInterface;
use App\Domains\Auth\Repositories\IAuthRepository;

class AuthRepository implements IAuthRepository
{
    protected $db;

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function findByEmail(string $email)
    {
        return $this->db->table('Usuarios')
            ->select('id_usuario, nombre, email, password_hash, rol, status, last_login')
            ->where('email', $email)
            ->where('status', 'active')
            ->get()
            ->getRow();
    }

    public function createUser(array $data): array
    {
        $this->db->transStart();

        // Insertar usuario
        $userData = [
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'rol' => $data['rol'],
            'status' => 'active'
        ];

        $this->db->table('Usuarios')->insert($userData);
        $userId = $this->db->insertID();

        // Si es un cliente, crear registro en tabla Clientes
        if ($data['rol'] === 'cliente') {
            $clienteData = [
                'id_usuario' => $userId,
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'direccion' => $data['direccion'] ?? null,
                'telefono' => $data['telefono'] ?? null
            ];
            $this->db->table('Clientes')->insert($clienteData);
        }

        // Si es un comercio, crear registro en tabla Comercios
        else if ($data['rol'] === 'comercio') {
            $comercioData = [
                'id_usuario' => $userId,
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'direccion' => $data['direccion'],
                'latitud' => $data['latitud'] ?? 0,
                'longitud' => $data['longitud'] ?? 0,
                'radio_cercania' => $data['radio_cercania'] ?? 1000,
                'telefono' => $data['telefono'] ?? null
            ];
            $this->db->table('Comercios')->insert($comercioData);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Error al crear el usuario');
        }

        return array_merge(['id_usuario' => $userId], $userData);
    }

    public function saveRefreshToken(int $userId, string $token): void
    {
        $expiresAt = date('Y-m-d H:i:s', time() + getenv('JWT_REFRESH_EXPIRATION'));

        $this->db->table('refresh_tokens')->insert([
            'id_usuario' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    public function updateLastLogin(int $userId): void
    {
        $this->db->table('Usuarios')
            ->where('id_usuario', $userId)
            ->update(['last_login' => date('Y-m-d H:i:s')]);
    }
}