<?php

namespace App\Domains\Clientes\Services;

use App\Domains\Clientes\Entities\Cliente;
use App\Domains\Clientes\Entities\Pedido;
use App\Infrastructure\Persistence\ClienteRepository;

class ClienteService
{
    protected $clienteRepository;

    public function __construct()
    {
        $this->clienteRepository = new ClienteRepository();
    }

    public function registrarCliente(array $datos): Cliente
    {
        $cliente = new Cliente($datos);

        // Verificar si ya existe el email
        if ($this->clienteRepository->findByEmail($cliente->email)) {
            throw new \DomainException('El email ya está registrado');
        }

        $id = $this->clienteRepository->insert($cliente);
        return $this->clienteRepository->find($id);
    }

    public function crearPedido(Cliente $cliente, array $datosOrden): Orden
    {
        $orden = new Pedido($datosOrden);
        $orden->id_cliente = $cliente->id_cliente;

        // Aquí iría la lógica de validación de disponibilidad y distancia

        return $orden;
    }
}