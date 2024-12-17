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

        // Validar que exista el token
        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')
                ->setJSON(['success' => false, 'message' => 'Token no proporcionado'])
                ->setStatusCode(401);
        }

        try {
            $jwtService = new JWTService();
            $token = $matches[1];
            $decoded = $jwtService->validateToken($token);

            // Validar el tipo de token
            if (!isset($decoded->type) || $decoded->type !== 'access') {
                throw new \Exception('Tipo de token inválido');
            }

            // Conectar a la base de datos
            $db = \Config\Database::connect();

            // Verificar si el usuario existe y está activo
            $usuario = $db->table('Usuarios')
                ->select('id_usuario, status')
                ->where('id_usuario', $decoded->sub ?? 0)
                ->where('status', 'active')
                ->get()
                ->getRow();

            if (!$usuario) {
                throw new \Exception('Usuario no encontrado o inactivo');
            }

            // Verificar roles de cliente y comercio
            $cliente = $db->table('Clientes')
                ->select('id_cliente')
                ->where('id_usuario', $decoded->sub)
                ->get()
                ->getRow();

            $comercio = $db->table('Comercios')
                ->select('id_comercio')
                ->where('id_usuario', $decoded->sub)
                ->get()
                ->getRow();

            // Evitar campos null en $request->user
            $request->user = (object)[
                'id' => $decoded->sub,
                'role' => $decoded->role ?? 'unknown',
                'type' => $cliente ? 'cliente' : ($comercio ? 'comercio' : 'unknown'),
                'id_cliente' => $cliente->id_cliente ?? null,
                'id_comercio' => $comercio->id_comercio ?? null
            ];

            if ($request->user->type === 'unknown') {
                throw new \Exception('Usuario sin rol asignado.');
            }

            return $request;

        } catch (\Exception $e) {
            return service('response')
                ->setJSON(['success' => false, 'message' => $e->getMessage()])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
