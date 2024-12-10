<?php
namespace App\Application\UsesCases\Comercios;

use App\Infrastructure\Persistence\OfertaMySQLRepository;

class CrearOferta
{
    private $ofertaRepository;

    public function __construct()
    {
        $this->ofertaRepository = new OfertaMySQLRepository();
    }

    public function ejecutar(array $datos): array
    {
        // Validar datos de la oferta
        $this->validarDatosOferta($datos);

        // Verificar que no exista una oferta activa para el producto
        if ($this->ofertaRepository->verificarOfertaActiva($datos['id_producto'])) {
            throw new \DomainException('Ya existe una oferta activa para este producto');
        }

        // Crear la oferta
        $oferta = [
            'id_producto' => $datos['id_producto'],
            'descuento_porcentaje' => $datos['descuento'],
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin']
        ];

        $resultado = $this->ofertaRepository->insert($oferta);

        return [
            'success' => true,
            'id_oferta' => $resultado,
            'mensaje' => 'Oferta creada correctamente'
        ];
    }

    private function validarDatosOferta(array $datos): void
    {
        if (!isset($datos['id_producto'], $datos['descuento'], $datos['fecha_inicio'], $datos['fecha_fin'])) {
            throw new \InvalidArgumentException('Faltan datos requeridos para la oferta');
        }

        if ($datos['descuento'] <= 0 || $datos['descuento'] >= 100) {
            throw new \InvalidArgumentException('El descuento debe estar entre 0 y 100');
        }

        $fechaInicio = strtotime($datos['fecha_inicio']);
        $fechaFin = strtotime($datos['fecha_fin']);

        if ($fechaFin <= $fechaInicio) {
            throw new \InvalidArgumentException('La fecha de fin debe ser posterior a la fecha de inicio');
        }
    }
}