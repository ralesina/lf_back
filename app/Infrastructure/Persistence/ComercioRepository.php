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
        return $this->db->table('Comercios')->where('id_comercio', $id)->get()->getRowArray();
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
    public function findByCategoria(string $categoria, ?array $filters = null): array
    {
        $builder = $this->db->table('Comercios c')
            ->select('c.*, 
                 COALESCE(AVG(p.precio), 0) as precio_promedio,
                 COUNT(DISTINCT p.id_producto) as total_productos')
            ->join('Productos p', 'p.id_comercio = c.id_comercio', 'left')
            ->where('c.categoria', $categoria)
            ->where('c.estado', 'activo');

        // Aplicar filtros adicionales si existen
        if (!empty($filters)) {
            if (isset($filters['nombre'])) {
                $builder->like('c.nombre', $filters['nombre']);
            }

            if (isset($filters['ordenar_por'])) {
                switch ($filters['ordenar_por']) {
                    case 'nombre':
                        $builder->orderBy('c.nombre', 'ASC');
                        break;
                    case 'productos':
                        $builder->orderBy('total_productos', 'DESC');
                        break;
                    case 'precio':
                        $builder->orderBy('precio_promedio', isset($filters['orden']) && $filters['orden'] === 'DESC' ? 'DESC' : 'ASC');
                        break;
                }
            }

            if (isset($filters['limite'])) {
                $builder->limit($filters['limite']);
            }
        }

        $builder->groupBy('c.id_comercio');

        return $builder->get()->getResultArray();
    }

    public function getDestacados(): array
    {
        return $this->db->table('Comercios')
            ->select('id_comercio, nombre, direccion, id_categoria, email, telefono')
            ->limit(10)
            ->get()
            ->getResultArray();
    }
    public function registrarProducto(array $data, ?string $imagenUrl = null): array
    {
        $this->db->transStart();

        try {
            // Verificar que la categoría existe
            $categoria = $this->db->table('Categorias')
                ->where('id_categoria', $data['id_categoria'])
                ->get()
                ->getRowArray();

            if (!$categoria) {
                throw new \RuntimeException('La categoría seleccionada no existe');
            }

            $productoData = [
                'id_comercio' => $data['id_comercio'],
                'nombre_producto' => $data['nombre_producto'],
                'descripcion' => $data['descripcion'] ?? null,
                'precio' => $data['precio'],
                'id_categoria' => $data['id_categoria'],
                'imagen_url' => $imagenUrl
            ];

            $insertProducto = $this->db->table('Productos')->insert($productoData);

            if (!$insertProducto) {
                throw new \RuntimeException('Error al insertar el producto');
            }

            $idProducto = $this->db->insertID();

            if (!$idProducto) {
                throw new \RuntimeException('No se pudo obtener el ID del producto');
            }

            // 2. Después insertar en inventario usando el ID obtenido
            $inventarioData = [
                'id_producto' => $idProducto,
                'cantidad' => $data['stock'] ?? 0,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            ];

            $insertInventario = $this->db->table('Inventario')->insert($inventarioData);

            if (!$insertInventario) {
                throw new \RuntimeException('Error al insertar el inventario');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Error en la transacción');
            }

            // Retornar el producto con su ID
            return array_merge(['id_producto' => $idProducto], $productoData);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error al registrar producto: ' . $e->getMessage());
            throw new \RuntimeException('Error al registrar el producto: ' . $e->getMessage());
        }
    }
    public function getCategorias(): array
    {
        return $this->db->table('Categorias')
            ->select('id_categoria as id, nombre_categoria')
            ->get()
            ->getResultArray();
    }

    public function buscarPorFiltros(array $filters): array
    {
        $builder = $this->db->table('Comercios')
            ->select('id_comercio, nombre, direccion, categoria, email, telefono');

        if (!empty($filters['categoria'])) {
            $builder->where('categoria', $filters['categoria']);
        }

        if (!empty($filters['busqueda'])) {
            $builder->groupStart()
                ->like('nombre', $filters['busqueda'])
                ->orLike('direccion', $filters['busqueda'])
                ->groupEnd();
        }

        $builder->where('status', 'active');

        return $builder->get()->getResultArray();
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
