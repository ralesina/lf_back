<?php

namespace App\Domains\Comercios\Repositories;

interface IInventarioRepository
{
    public function findByProducto(int $idProducto): ?array;

    public function actualizarCantidad(int $idProducto, int $cantidad): bool;

    public function registrarMovimiento(int $idProducto, int $cantidad, string $tipo, string $motivo): bool;
}