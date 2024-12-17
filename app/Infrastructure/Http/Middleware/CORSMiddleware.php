<?php
namespace App\Infrastructure\Http\Middleware;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Cors as CorsConfig;

class CORSMiddleware implements FilterInterface
{
    protected $corsConfig;

    public function __construct()
    {
        $this->corsConfig = new CorsConfig();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = service('response');
            $origin = $request->getHeaderLine('Origin');

            // Limpiar cualquier header CORS previo
            $response->removeHeader('Access-Control-Allow-Origin');
            $response->removeHeader('Access-Control-Allow-Methods');
            $response->removeHeader('Access-Control-Allow-Headers');

            // Establecer headers CORS
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            $response->setHeader('Access-Control-Max-Age', '86400');

            return $response->setStatusCode(204);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin');

        // Limpiar y establecer headers CORS para respuestas no-OPTIONS
        $response->removeHeader('Access-Control-Allow-Origin');
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        $response->setHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
