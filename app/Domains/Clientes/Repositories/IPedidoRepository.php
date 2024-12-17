<?php
namespace App\Domains\Clientes\Repositories;

interface IPedidoRepository
{
    public function findById(int $id): array;
    public function findByCliente(int $idCliente): array;
    public function findByComercio(int $idComercio): array;
    public function findByComercioAndEstado(int $idComercio, ?string $estado = null): array;
    public function getPedidoConDetalles(int $idPedido, int $idCliente): array;

    public function createPedidoCompleto(array $pedidoData, array $detalles): array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}