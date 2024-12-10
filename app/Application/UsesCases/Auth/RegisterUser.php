<?php

namespace App\Application\UsesCases\Auth;

use App\Domains\Auth\Repositories\IAuthRepository;
use App\Domains\Auth\Entities\Usuario;
use App\Domains\Auth\Services\AuthService;
use App\Exceptions\ValidationException;

class RegisterUser
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function execute(array $data): array
    {
        return $this->authService->registerUser($data);
    }
}
