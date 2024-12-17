<?php

namespace App\Application\UsesCases\Auth;

use App\Domains\Auth\Services\AuthService;

class GetPerfil
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function execute(int $userId): array
    {
        return $this->authService->getPerfil($userId);
    }
}