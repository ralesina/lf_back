<?php
namespace App\Infrastructure\Persistence;

use CodeIgniter\Database\ConnectionInterface;
use App\Domains\Clientes\Repositories\IClienteRepository;
use App\Domains\Clientes\Repositories\IPedidoRepository;

class ClienteRepository implements IClienteRepository
{
    protected $db;

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function findById(int $id): ?array
    {
        return $this->db->table('clientes')->where('id_cliente', $id)->get()->getRowArray();
    }

    public function create(array $data): array
    {
        $this->db->table('clientes')->insert($data);
        $data['id_cliente'] = $this->db->insertID();
        return $data;
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->table('clientes')->where('id_cliente', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->db->table('clientes')->where('id_cliente', $id)->delete();
    }
}

