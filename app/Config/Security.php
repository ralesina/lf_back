<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    public string $tokenName  = 'csrf_token_name';
    public string $headerName = 'X-CSRF-TOKEN';
    public string $cookieName = 'csrf_cookie_name';
    public $expires    = 7200;
    public bool $regenerate = true;
    public bool $redirect   = false;
    public string $samesite   = 'Lax';

    // Desactivar CSRF para API
    public string $csrfProtection = 'cookie';

    public array $excludeURIs = ['api/*'];
}