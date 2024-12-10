<?php
namespace App\Infrastructure\Persistence;

use CodeIgniter\Database\ConnectionInterface;
use App\Domains\Comercios\Repositories\IComercioRepository;
use App\Domains\Comercios\Repositories\IProductoRepository;

class ComercioRepository implements IComercioRepository
{
    protected $db;

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function findById(int $id): ?array
    {
        return $this->db->table('comercios')->where('id_comercio', $id)->get()->getRowArray();
    }
    public function findNearby(float $latitud, float $longitud, float $radio): array
    {
        $query = "
            SELECT *, 
            (6371 * ACOS(
                COS(RADIANS(:latitud:)) * COS(RADIANS(latitud)) *
                COS(RADIANS(longitud) - RADIANS(:longitud:)) +
                SIN(RADIANS(:latitud:)) * SIN(RADIANS(latitud))
            )) AS distancia
            FROM Comercios
            HAVING distancia <= :radio:
            ORDER BY distancia ASC
        ";

        return $this->db->query($query, [
            'latitud' => $latitud,
            'longitud' => $longitud,
            'radio' => $radio
        ])->getResultArray();
    }
    public function findByUsuario(int $idUsuario): ?array
    {
        return $this->db->table('Comercios')
            ->where('id_usuario', $idUsuario)
            ->get()
            ->getRowArray();
    }

    public function create(array $data): array
    {
        $this->db->table('comercios')->insert($data);
        $data['id_comercio'] = $this->db->insertID();
        return $data;
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->table('comercios')->where('id_comercio', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->db->table('comercios')->where('id_comercio', $id)->delete();
    }
}
