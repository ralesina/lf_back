<?php

namespace App\Domains\Auth\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $attributes = [
        'id_usuario' => null,
        'nombre' => null,
        'email' => null,
        'password_hash' => null,
        'rol' => null,
        'status' => null,
        'last_login' => null
    ];

    protected $casts = [
        'id_usuario' => 'integer',
        'status' => 'string',
        'last_login' => 'datetime'
    ];

    public function setPassword(string $password)
    {
        $this->attributes['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password_hash']);
    }
}
