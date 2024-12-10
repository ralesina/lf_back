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
        $this->db->transStart();

        try {
            // Insertar el pedido
            $this->db->table('Pedidos')->insert($pedidoData);
            $idPedido = $this->db->insertID();

            // Insertar los detalles
            foreach ($detalles as $detalle) {
                $detalle['id_pedido'] = $idPedido;
                $this->db->table('PedidoDetalles')->insert($detalle);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Error al crear el pedido');
            }

            // Retornar el pedido completo
            return $this->getPedidoConDetalles($idPedido);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function getPedidoConDetalles(int $idPedido): array
    {
        $pedido = $this->findById($idPedido);

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


    public function findById(int $id): ?array
    {
        return $this->db->table('pedidos')->where('id_pedido', $id)->get()->getRowArray();
    }

    public function findByCliente(int $idCliente): array
    {
        return $this->db->table('pedidos')->where('id_cliente', $idCliente)->get()->getResultArray();
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

    public function update(int $id, array $data): bool
    {
        return $this->db->table('pedidos')->where('id_pedido', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->db->table('pedidos')->where('id_pedido', $id)->delete();
    }
}
