<?php
namespace App\Application\UsesCases\Auth;

use App\Domains\Auth\Services\AuthService;
use App\Exceptions\AuthenticationException;

class RefreshToken
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function execute(string $refreshToken): array
    {
        return $this->authService->refreshToken($refreshToken);
    }
}
