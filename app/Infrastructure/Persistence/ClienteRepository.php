<?php
namespace App\Infrastructure\Persistence;

use CodeIgniter\Database\ConnectionInterface;
use App\Domains\Clientes\Repositories\IClienteRepository;

class ClienteRepository implements IClienteRepository
{
    protected $db;

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function findById(int $id): ?array
    {
        return $this->db->table('Clientes')
            ->where('id_cliente', $id)
            ->get()
            ->getRowArray();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->table('Clientes')
            ->where('email', $email)
            ->get()
            ->getRowArray();
    }

    public function findByUsuario(int $idUsuario): ?array
    {
        return $this->db->table('Clientes')
            ->where('id_usuario', $idUsuario)
            ->get()
            ->getRowArray();
    }
    public function getPedidosCliente(int $idCliente): array
    {
        return $this->db->table('Pedidos p')
            ->select('p.*, c.nombre as comercio_nombre')
            ->join('Comercios c', 'c.id_comercio = p.id_comercio')
            ->where('p.id_cliente', $idCliente)
            ->orderBy('p.fecha_pedido', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function create(array $data): array
    {
        $this->db->table('Clientes')->insert($data);
        $data['id_cliente'] = $this->db->insertID();
        return $data;
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->table('Clientes')
            ->where('id_cliente', $id)
            ->update($data);
    }
    public function delete(int $id): bool
    {
        return $this->db->table('clientes')->where('id_cliente', $id)->delete();
    }
}

