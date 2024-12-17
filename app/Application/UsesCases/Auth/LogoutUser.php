<?php
namespace App\Application\UsesCases\Auth;

use App\Domains\Auth\Services\AuthService;

class LogoutUser
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function execute(int $userId): bool
    {
        return $this->authService->logout($userId);
    }
}