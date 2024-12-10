<?php
namespace App\Exceptions;

class DomainException extends \Exception
{
    protected $code = 422;
}