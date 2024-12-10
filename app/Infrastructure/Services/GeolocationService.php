<?php
namespace App\Infrastructure\Services;

class GeolocationService
{
    public function calcularDistancia(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000; // metros

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function estaDentroDeRadio(
        float $latComercio,
        float $lonComercio,
        float $latCliente,
        float $lonCliente,
        float $radioMetros
    ): bool {
        $distancia = $this->calcularDistancia(
            $latComercio,
            $lonComercio,
            $latCliente,
            $lonCliente
        );

        return $distancia <= $radioMetros;
    }
}
