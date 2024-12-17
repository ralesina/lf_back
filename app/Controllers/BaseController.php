<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Exceptions\{ValidationException, AuthenticationException, DomainException};

class BaseController extends ResourceController
{
    protected $helpers = [];
    protected $format = 'json';

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }
    protected function successResponse($data = null, string $message = 'Success'): array
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message
        ];
    }

    protected function errorResponse(string $message, array $errors = [], int $code = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
    }
    protected function executeUseCase(callable $useCase)
    {
        try {
            $result = $useCase();
            return $this->respond([
                'success' => true,
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return $this->respond([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (AuthenticationException $e) {
            return $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (DomainException $e) {
            return $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            log_message('error', '[Error] ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Ha ocurrido un error interno del servidor'
            ], 500);
        }
    }
    public function options()
    {
        return $this->response->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->setStatusCode(204);
    }
}