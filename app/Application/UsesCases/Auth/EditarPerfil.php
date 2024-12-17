<?php

namespace App\Application\UsesCases\Auth;

use App\Domains\Auth\Services\AuthService;

class EditarPerfil
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function execute(int $userId, array $data): array
    {
        return $this->authService->editarPerfil($userId, $data);
    }
}