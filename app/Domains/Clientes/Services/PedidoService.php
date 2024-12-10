<?php
namespace App\Domains\Clientes\Services;

use App\Domains\Clientes\Repositories\IPedidoRepository;
use App\Domains\Comercios\Repositories\IProductoRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\DomainException;

class PedidoService
{
    private $pedidoRepository;
    private $productoRepository;

    public function __construct(
        IPedidoRepository $pedidoRepository,
        IProductoRepository $productoRepository
    ) {
        $this->pedidoRepository = $pedidoRepository;
        $this->productoRepository = $productoRepository;
    }

    public function crearPedido(array $data): array
    {
        $this->validarDatosPedido($data);

        // Calcular total y validar productos
        $detalles = $this->procesarDetallesPedido($data['items']);

        $pedidoData = [
            'id_cliente' => $data['id_cliente'],
            'id_comercio' => $data['id_comercio'],
            'direccion_entrega' => $data['direccion_entrega'],
            'telefono_contacto' => $data['telefono_contacto'],
            'total' => $detalles['total'],
            'estado' => 'pendiente',
            'instrucciones' => $data['instrucciones'] ?? null,
            'metodo_pago' => $data['metodo_pago']
        ];

        return $this->pedidoRepository->createPedidoCompleto($pedidoData, $detalles['items']);
    }

    private function procesarDetallesPedido(array $items): array
    {
        $detallesProcesados = [];
        $total = 0;

        foreach ($items as $item) {
            $producto = $this->productoRepository->findById($item['id_producto']);

            if (!$producto) {
                throw new DomainException('Producto no encontrado');
            }

            if (!$this->verificarDisponibilidadProducto($producto, $item['cantidad'])) {
                throw new DomainException("Producto {$producto['nombre_producto']} sin stock suficiente");
            }

            $subtotal = $producto['precio'] * $item['cantidad'];
            $total += $subtotal;

            $detallesProcesados[] = [
                'id_producto' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto['precio']
            ];
        }

        return [
            'items' => $detallesProcesados,
            'total' => $total
        ];
    }

    private function verificarDisponibilidadProducto(array $producto, int $cantidad): bool
    {
        // Aquí podríamos verificar el stock si lo manejamos
        return true;
    }

    private function validarDatosPedido(array $data): void
    {
        $errors = [];

        if (empty($data['id_cliente'])) {
            $errors['id_cliente'] = 'El cliente es requerido';
        }

        if (empty($data['id_comercio'])) {
            $errors['id_comercio'] = 'El comercio es requerido';
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            $errors['items'] = 'Debe incluir al menos un item en el pedido';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}