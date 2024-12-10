<?php
namespace App\Exceptions;

class ValidationException extends \Exception
{
    protected $code = 400;
    protected $errors;

    public function __construct(array $errors, string $message = "Validation failed")
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}