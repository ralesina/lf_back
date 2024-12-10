<?php

namespace App\Application\UsesCases\Auth;

use App\Domains\Auth\Repositories\IAuthRepository;
use App\Domains\Auth\Entities\Usuario;
use App\Domains\Auth\Services\AuthService;
use App\Exceptions\AuthenticationException;

class AuthenticateUser
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function execute(string $email, string $password): array
    {
        return $this->authService->authenticateUser($email, $password);
    }
}