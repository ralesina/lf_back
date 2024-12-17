<?php
namespace App\Domains\Clientes\Services;

use App\Domains\Clientes\Repositories\IClienteRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\DomainException;

class ClienteService
{
    private $clienteRepository;

    public function __construct(IClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    public function registrarCliente(array $data): array
    {
        $this->validarDatosCliente($data);

        // Verificar si existe el cliente
        if ($this->clienteRepository->findByEmail($data['email'])) {
            throw new DomainException('El email ya está registrado');
        }

        return $this->clienteRepository->create($data);
    }

    public function actualizarCliente(int $idCliente, array $data): bool
    {
        $this->validarDatosCliente($data);

        $cliente = $this->clienteRepository->findById($idCliente);
        if (!$cliente) {
            throw new DomainException('Cliente no encontrado');
        }

        return $this->clienteRepository->update($idCliente, $data);
    }

    public function getPedidosCliente(int $idCliente): array
    {
        return $this->clienteRepository->getPedidosCliente($idCliente);
    }

    private function validarDatosCliente(array $data): void
    {
        $errors = [];

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        }

        if (!empty($data['telefono']) && !preg_match('/^\d{9}$/', $data['telefono'])) {
            $errors['telefono'] = 'Teléfono inválido';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}