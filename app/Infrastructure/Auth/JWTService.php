<?php

namespace App\Infrastructure\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Auth;

class JWTService
{
    private $config;
    private $key;

    public function __construct()
    {
        $this->config = config('Auth');
        $this->key = $this->config->jwtSecret;
    }

    public function generateTokens(int $userId, string $role): array
    {
        return [
            'access_token' => $this->createAccessToken($userId, $role),
            'refresh_token' => $this->createRefreshToken($userId),
            'expires_in' => $this->config->jwtTTL
        ];
    }

    private function createAccessToken(int $userId, string $role): string
    {
        $payload = [
            'sub' => $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + $this->config->jwtTTL,
            'type' => 'access'
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    private function createRefreshToken(int $userId): string
    {
        $payload = [
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $this->config->jwtRefreshTTL,
            'type' => 'refresh'
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function validateToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->key, 'HS256'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\AuthenticationException('Token inválido');
        }
    }
}