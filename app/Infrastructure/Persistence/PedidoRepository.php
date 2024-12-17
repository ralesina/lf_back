<?php
namespace App\Infrastructure\Persistence;

use App\Domains\Clientes\Repositories\IPedidoRepository;
use CodeIgniter\Database\ConnectionInterface;

class PedidoRepository implements IPedidoRepository
{
    protected $db;

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function createPedidoCompleto(array $pedidoData, array $detalles): array
    {
        $this->db->transBegin();

        try {
            // Asegurar que tenemos los campos necesarios para el pedido
            $pedidoInsert = [
                'id_cliente' => $pedidoData['id_cliente'],
                'id_comercio' => $pedidoData['id_comercio'],
                'direccion_entrega' => $pedidoData['direccion_entrega'],
                'telefono_contacto' => $pedidoData['telefono_contacto'],
                'total' => $pedidoData['total'],
                'estado' => 'pendiente',
                'instrucciones' => $pedidoData['instrucciones'] ?? null,
                'metodo_pago' => $pedidoData['metodo_pago'],
                'fecha_pedido' => date('Y-m-d H:i:s')
            ];

            // Insertar el pedido
            $success = $this->db->table('Pedidos')->insert($pedidoInsert);
            if (!$success) {
                throw new \RuntimeException('Error al insertar el pedido');
            }

            $idPedido = $this->db->insertID();

            // Insertar los detalles uno a uno
            foreach ($detalles as $detalle) {
                $detalleInsert = [
                    'id_pedido' => $idPedido,
                    'id_producto' => $detalle['id_producto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario']
                ];

                $success = $this->db->table('PedidoDetalles')->insert($detalleInsert);
                if (!$success) {
                    throw new \RuntimeException('Error al insertar detalle de pedido');
                }
            }

            // Si todo salió bien, confirmar la transacción
            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Error en la transacción');
            }

            $this->db->transCommit();

            // Retornar el pedido completo
            return $this->getPedidoConDetalles($idPedido);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error al crear pedido: ' . $e->getMessage());
            throw new \RuntimeException('Error al crear el pedido: ' . $e->getMessage());
        }
    }

    public function getPedidoConDetalles(int $idPedido, int $idCliente): array
    {
        $pedido = $this->db->table('Pedidos')
            ->where('id_pedido', $idPedido)
            ->get()
            ->getRowArray();

        if (!$pedido) {
            throw new \RuntimeException('Error al recuperar el pedido');
        }

        $detalles = $this->db->table('PedidoDetalles pd')
            ->select('pd.*, p.nombre_producto')
            ->join('Productos p', 'p.id_producto = pd.id_producto')
            ->where('pd.id_pedido', $idPedido)
            ->get()
            ->getResultArray();

        $pedido['detalles'] = $detalles;
        return $pedido;
    }


    public function findById(int $idPedido): array
    {
        $query = $this->db->table('Pedidos')
            ->where('id_pedido', $idPedido)
            ->get();

        return $query->getRowArray();
    }

    public function findByCliente(int $idCliente): array
    {
        return $this->db->table('Pedidos')->where('id_cliente', $idCliente)->get()->getResultArray();
    }
    public function findByComercio(int $idComercio): array
    {
        return $this->db->table('Pedidos p')
            ->select('p.*, c.nombre as cliente_nombre, c.email as cliente_email, 
                 GROUP_CONCAT(pd.id_producto) as productos_ids,
                 GROUP_CONCAT(pd.cantidad) as cantidades,
                 GROUP_CONCAT(pr.nombre_producto) as productos_nombres')
            ->join('Clientes c', 'c.id_cliente = p.id_cliente')
            ->join('PedidoDetalles pd', 'pd.id_pedido = p.id_pedido')
            ->join('Productos pr', 'pr.id_producto = pd.id_producto')
            ->where('p.id_comercio', $idComercio)
            ->groupBy('p.id_pedido')
            ->orderBy('p.fecha_pedido', 'DESC')
            ->get()
            ->getResultArray();
    }
    public function findByComercioAndEstado(int $idComercio, ?string $estado = null): array
    {
        $builder = $this->db->table('Pedidos p')
            ->select('p.*, c.nombre as cliente_nombre, c.email as cliente_email')
            ->join('Clientes c', 'c.id_cliente = p.id_cliente')
            ->where('p.id_comercio', $idComercio);

        if ($estado) {
            $builder->where('p.estado', $estado);
        }

        $pedidos = $builder->orderBy('p.fecha_pedido', 'DESC')
            ->get()
            ->getResultArray();

        // Obtener los detalles para cada pedido
        foreach ($pedidos as &$pedido) {
            $detalles = $this->db->table('PedidoDetalles pd')
                ->select('pd.*, pr.nombre_producto')
                ->join('Productos pr', 'pr.id_producto = pd.id_producto')
                ->where('pd.id_pedido', $pedido['id_pedido'])
                ->get()
                ->getResultArray();

            $pedido['detalles'] = $detalles;
        }

        return $pedidos;
    }
    public function update(int $id, array $data): bool
    {
        return $this->db->table('Pedidos')->where('id_pedido', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->db->table('Pedidos')->where('id_pedido', $id)->delete();
    }
    public function findHistorialByCliente(int $idCliente): array
    {
        return $this->db->table('Pedidos p')
            ->select('p.*, c.nombre as comercio_nombre')
            ->join('Comercios c', 'c.id_comercio = p.id_comercio')
            ->where('p.id_cliente', $idCliente)
            ->orderBy('p.fecha_pedido', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function findActivosByCliente(int $idCliente): array
    {
        return $this->db->table('Pedidos p')
            ->select('p.*, c.nombre as comercio_nombre')
            ->join('Comercios c', 'c.id_comercio = p.id_comercio')
            ->where('p.id_cliente', $idCliente)
            ->whereIn('p.estado', ['pendiente',
                'confirmado',
                'en_preparacion',
                'en_camino',])
            ->orderBy('p.fecha_pedido', 'DESC')
            ->get()
            ->getResultArray();
    }
}
