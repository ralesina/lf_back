<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $allowedOrigins = [
        'http://localhost:4200',
        'https://localfresh.com'
    ];

    public array $allowedMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS',
        'PATCH'
    ];

    public array $allowedHeaders = [
        'Origin',
        'Content-Type',
        'Accept',
        'Authorization',
        'X-Requested-With'
    ];

    public bool $allowCredentials = true;
    public int $maxAge = 7200;
    public array $exposedHeaders = ['Authorization'];
}