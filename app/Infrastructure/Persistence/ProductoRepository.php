<?php
namespace App\Infrastructure\Persistence;

use CodeIgniter\Database\ConnectionInterface;
use App\Domains\Comercios\Repositories\IProductoRepository;

class ProductoRepository implements IProductoRepository
{
    protected $db;

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function findById(int $id): ?array
    {
        return $this->db->table('Productos p')
            ->select('p.*, i.cantidad as stock, c.nombre_categoria')
            ->join('Inventario i', 'i.id_producto = p.id_producto', 'left')
            ->join('Categorias c', 'c.id_categoria = p.id_categoria', 'left')
            ->where('p.id_producto', $id)
            ->get()
            ->getRowArray();
    }

    public function findByComercio(int $idComercio): array
    {
        $builder = $this->db->table('Productos p')
            ->select('p.*, i.cantidad as stock, c.nombre_categoria')
            ->join('Inventario i', 'i.id_producto = p.id_producto', 'left')
            ->join('Categorias c', 'c.id_categoria = p.id_categoria', 'left')
            ->where('p.id_comercio', $idComercio);

        // Log de la consulta SQL
        log_message('debug', 'SQL Query: ' . $this->db->getLastQuery());

        $result = $builder->get()->getResultArray();

        // Log del resultado
        log_message('debug', 'Query Result: ' . json_encode($result));

        return $result ?: [];
    }

    public function findByCategoriaAndComercio(int $idCategoria, int $idComercio): array
    {
        return $this->db->table('Productos p')
            ->select('p.*, i.cantidad as stock, c.nombre_categoria')
            ->join('Inventario i', 'i.id_producto = p.id_producto', 'left')
            ->join('Categorias c', 'c.id_categoria = p.id_categoria', 'left')
            ->where('p.id_comercio', $idComercio)
            ->where('p.id_categoria', $idCategoria)
            ->get()
            ->getResultArray();
    }

    public function create(array $data): array
    {
        $this->db->transStart();

        try {
            // Insertar producto
            $productoData = [
                'id_comercio' => $data['id_comercio'],
                'nombre_producto' => $data['nombre_producto'],
                'descripcion' => $data['descripcion'] ?? null,
                'precio' => $data['precio'],
                'categoria' => $data['categoria'] ?? null,
                'id_categoria' => $data['id_categoria'] ?? null
            ];

            $this->db->table('Productos')->insert($productoData);
            $idProducto = $this->db->insertID();

            // Crear registro de inventario inicial
            $inventarioData = [
                'id_producto' => $idProducto,
                'cantidad' => $data['stock'] ?? 0
            ];

            $this->db->table('Inventario')->insert($inventarioData);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Error al crear el producto');
            }

            return $this->findById($idProducto);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function update(int $id, array $data): array
    {
        $this->db->transStart();

        try {
            // Preparar datos del producto
            $this->db->table('Productos')
                ->where('id_producto', $id)
                ->update($data);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Error al actualizar el producto');
            }

            return $this->findById($id);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        return $this->db->table('Productos')
            ->where('id_producto', $id)
            ->update(['estado' => $estado]);
    }

    public function updateStock(int $id, int $cantidad): bool
    {
        $this->db->transStart();

        try {
            $inventario = $this->db->table('Inventario')
                ->where('id_producto', $id)
                ->get()
                ->getRow();

            if ($inventario) {
                $this->db->table('Inventario')
                    ->where('id_producto', $id)
                    ->update(['cantidad' => $cantidad]);
            } else {
                $this->db->table('Inventario')->insert([
                    'id_producto' => $id,
                    'cantidad' => $cantidad
                ]);
            }

            $this->db->transComplete();
            return $this->db->transStatus();

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        return $this->db->table('Productos')
            ->where('id_producto', $id)
            ->delete();
    }

    public function existsInComercio(int $idProducto, int $idComercio): bool
    {
        return $this->db->table('Productos')
                ->where('id_producto', $idProducto)
                ->where('id_comercio', $idComercio)
                ->countAllResults() > 0;
    }
}