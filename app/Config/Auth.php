<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    public $jwtSecret;
    public $jwtTTL = 3600; // 1 hora
    public $jwtRefreshTTL;

    public function __construct()
    {
        parent::__construct();
        $this->jwtSecret = getenv('JWT_SECRET_KEY');
        $this->jwtRefreshTTL = (int)getenv('JWT_REFRESH_EXPIRATION');
    }
}