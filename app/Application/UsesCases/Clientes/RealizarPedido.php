<?php
namespace App\Application\UsesCases\Clientes;

use App\Domains\Clientes\Services\PedidoService;
use App\Domains\Clientes\Entities\Pedido;
use App\Exceptions\ValidationException;
use App\Domains\Clientes\Repositories\IClienteRepository;

class RealizarPedido
{
    private $pedidoService;
    private $clienteRepository;

    public function __construct(
        PedidoService $pedidoService,
        IClienteRepository $clienteRepository
    ) {
        $this->pedidoService = $pedidoService;
        $this->clienteRepository = $clienteRepository;
    }

    public function execute(array $data): array
    {
        $errors = [];
        if (isset($data['id_usuario']) && !isset($data['id_cliente'])) {
            $cliente = $this->clienteRepository->findByUsuario($data['id_usuario']);
            if (!$cliente) {
                $errors['id_cliente'] = 'Cliente no encontrado para este usuario';
            } else {
                $data['id_cliente'] = $cliente['id_cliente'];
            }
        }

        if (!isset($data['id_cliente'])) {
            $errors['id_cliente'] = 'El ID del cliente es requerido';
        }

        if (!isset($data['items']) && !isset($data['productos'])) {
            $errors['productos'] = 'Debe incluir al menos un producto';
        }

        if (isset($data['productos']) && !isset($data['items'])) {
            $data['items'] = $data['productos'];
            unset($data['productos']);
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->pedidoService->crearPedido($data);
    }
}