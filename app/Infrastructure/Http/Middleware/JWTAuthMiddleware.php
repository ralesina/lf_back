<?php
namespace App\Infrastructure\Http\Middleware;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Infrastructure\Auth\JWTService;

class JWTAuthMiddleware implements \CodeIgniter\Filters\FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')
                ->setJSON([
                    'success' => false,
                    'message' => 'Token no proporcionado'
                ])
                ->setStatusCode(401);
        }

        try {
            $jwtService = new JWTService();
            $token = $matches[1];
            $decoded = $jwtService->validateToken($token);

            if ($decoded->type !== 'access') {
                throw new \Exception('Tipo de token inválido');
            }

            // Verificar si el usuario está activo
            $db = \Config\Database::connect();
            $user = $db->table('Usuarios')
                ->where('id_usuario', $decoded->sub)
                ->where('status', 'active')
                ->get()
                ->getRow();

            if (!$user) {
                throw new \Exception('Usuario no encontrado o inactivo');
            }

            $request->user = (object) [
                'id' => $decoded->sub,
                'role' => $decoded->role
            ];

            return $request;
        } catch (\Exception $e) {
            return service('response')
                ->setJSON([
                    'success' => false,
                    'message' => 'Token inválido o expirado'
                ])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}