<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ExceptionFilter
    implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    public function handleException(Throwable $e): ResponseInterface
    {
        $statusCode = 500;
        $message = 'Internal Server Error';
        if ($e instanceof DomainException || $e instanceof ValidationException) {
            $statusCode = 400;
            $message = $e->getMessage();
        }
        return service('response')->setStatusCode($statusCode)->setJSON(['error' => $message]);
    }
}
