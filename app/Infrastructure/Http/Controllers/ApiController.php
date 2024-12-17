<?php

namespace App\Infrastructure\Http\Controllers;

use App\Controllers\BaseController;
use App\Domains\Clientes\Entities\Pedido;
use App\Exceptions\DomainException;
use App\Exceptions\ValidationException;
use App\Infrastructure\Auth\JWTService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiController extends BaseController
{
    protected  $registerUser;
    protected  $authenticateUser;

    protected  $realizarPedido;
    protected  $consultarPedidos;
    protected  $buscarComercio;
    protected  $buscarCercano;
    protected  $cancelarPedido;
    protected  $gestionarPedido;
    protected  $listarPedidosComercio;
    protected $agregarProducto;
    protected $listarComerciosDestacados;
    protected $listarCategorias;
    protected $buscarComercios;
    protected $listarProductosComercio;
    protected $listarProductosComercios;
    protected $cambiarEstadoProducto;
    protected $editarProducto;
    protected $refreshToken;
    protected $logoutUser;
    protected $consultarPedidosActivos;
    protected $editarInventario;
    protected $consultarHistorialPedidos;
    protected $buscarComerciosPorFiltros;
    protected $editarPerfil;
    protected $getPerfil;
    protected $BuscarComercio;
    protected $consultarPedido;

    public function __construct() {
        $this->inicializarServicios();

    }
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->inicializarServicios();
    }
    public function register(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getJSON(true);
            return $this->registerUser->execute($data);
        });
    }

    public function login(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getJSON(true);
            return $this->authenticateUser->execute($data['email'], $data['password']);
        });
    }

    public function refreshToken(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $refreshToken = $this->request->getJsonVar('refresh_token');
            return $this->refreshToken->execute($refreshToken);
        });
    }

    public function logout(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            return $this->logoutUser->execute($this->request->user->id);
        });
    }
    public function getPerfil(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            return $this->getPerfil->execute($this->request->user->id);
        });
    }

    public function editarPerfil(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getJSON(true);
            return $this->editarPerfil->execute($this->request->user->id, $data);
        });
    }
    // Cliente - Pedidos
    public function realizarPedido(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getJSON(true);
            return $this->realizarPedido->execute($data);
        });
    }

    public function consultarPedidosActivos(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $user = JWTService::getUser();

            // Validar el tipo de usuario
            if ($user['type'] === 'cliente' && isset($user['id_cliente'])) {
                return $this->consultarPedidosActivos->execute($user['id_cliente']);
            }

            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Solo los clientes pueden consultar pedidos activos.'
            ]);
        });
    }
    public function consultarPedidoDetalle(int $idPedido): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idPedido){
            $user = JWTService::getUser();

            if ($user['type'] === 'cliente') {
                return $this->consultarPedido->execute($idPedido, $user['id_cliente']);
            }

            return [
                'success' => false,
                'message' => 'Solo los clientes pueden consultar pedidos activos.'
            ];
        });
    }
    public function consultarHistorialPedidos(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $user = JWTService::getUser();

            if ($user['type'] === 'cliente') {
                return $this->consultarHistorialPedidos->execute($user['id_cliente']);
            }

            return [
                'success' => false,
                'message' => 'Solo los clientes pueden consultar el historial de pedidos.'
            ];
        });
    }

    public function cancelarPedido(int $idPedido): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idPedido) {
            $user = JWTService::getUser();
            return $this->cancelarPedido->execute($idPedido, $user['id_cliente']);
        });
    }

    // Cliente - Comercios
    public function buscarComerciosPorFiltros(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $filtros = $this->request->getGet();
            return $this->buscarComerciosPorFiltros->execute($filtros);
        });
    }

    public function buscarCercano(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $latitud = (float)$this->request->getGet('latitud');
            $longitud = (float)$this->request->getGet('longitud');
            $radio = (int)$this->request->getGet('radio');
            return $this->buscarCercano->execute($latitud, $longitud, $radio);
        });
    }

    public function comerciosDestacados(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            return $this->listarComerciosDestacados->execute();
        });
    }

    public function categorias(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            return $this->listarCategorias->execute();
        });
    }
    public function getDatosComercio($id_comercio): ResponseInterface
    {
        return $this->executeUseCase(function() use ($id_comercio) {
            return $this->buscarComercio->execute($id_comercio);
        });
    }
    public function listarProductosComercio($id_comercio): ResponseInterface
    {
        return $this->executeUseCase(function() use ($id_comercio) {
            return $this->listarProductosComercio->execute($id_comercio);
        });
    }
    // Comercio - Productos
    public function listarProductos(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            return $this->listarProductosComercios->execute($this->request->user->id);
        });
    }

    public function agregarProducto(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getPost(); // Cambiar a getPost para manejar multipart/form-data
            $data['id_usuario'] = $this->request->user->id;
            $imagen = $this->request->getFile('imagen');

            return $this->agregarProducto->execute($data, $imagen);
        });
    }

    public function editarProducto(int $idProducto): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idProducto) {
            $data = $this->request->getPost();
            $imagen = $this->request->getFile('imagen');

            return $this->editarProducto->execute($idProducto, $data, $imagen);
        });
    }

    public function cambiarEstadoProducto(int $idProducto): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idProducto) {
            $estado = $this->request->getJsonVar('estado');
            return $this->cambiarEstadoProducto->execute($idProducto, $estado);
        });
    }

    // Comercio - Inventario
    public function editarInventario(int $idProducto): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idProducto) {
            $datos = $this->request->getJSON(true);

            if (!isset($datos['stock'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'El stock es requerido'
                ]);
            }

            $resultado = $this->editarInventario->execute(
                (int)$idProducto,
                ['stock' => (int)$datos['stock']]
            );
            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Stock actualizado correctamente'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el stock'
                ]);
            }
        });

    }

    // Comercio - Pedidos
    public function listarPedidosComercio($estado = null): ResponseInterface
    {
        return $this->executeUseCase(function() use ($estado) {
            // Validar que el estado sea válido según los estados definidos en la entidad
            if ($estado && !in_array($estado, Pedido::ESTADOS_VALIDOS)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Estado no válido'
                ]);
            }

            return $this->listarPedidosComercio->execute(
                $this->request->user->id,
                $estado
            );
        });
    }

    public function gestionarPedido(int $idPedido): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idPedido) {
            $nuevoEstado = $this->request->getJsonVar('estado');

            if (!$nuevoEstado) {
                throw new ValidationException(['El campo "estado" es obligatorio.']);
            }

            $user = JWTService::getUser();

            if ($user['type'] !== 'comercio' || !isset($user['id_comercio'])) {
                throw new DomainException('El usuario no tiene permisos para gestionar pedidos.');
            }

            return $this->gestionarPedido->execute($idPedido, $user['id_comercio'], $nuevoEstado);
        });
    }
    /**
     * @return void
     */
    public function inicializarServicios(): void
    {
        $this->registerUser = service('registerUser');
        $this->authenticateUser = service('authenticateUser');
        $this->realizarPedido = service('realizarPedido');
        $this->consultarPedidos = service('consultarPedidos');
        $this->consultarPedido = service('consultarPedido');
        $this->buscarComercio = service('buscarComercio');
        $this->buscarCercano = service('buscarCercano');
        $this->cancelarPedido = service('cancelarPedido');
        $this->gestionarPedido = service('gestionarPedido');
        $this->listarPedidosComercio = service('listarPedidosComercio');
        $this->agregarProducto = service('agregarProducto');
        $this->listarComerciosDestacados = service('listarComerciosDestacados');
        $this->listarCategorias = service('listarCategorias');
        $this->buscarComercios = service('buscarComercios');
        $this->listarProductosComercio = service('listarProductosComercio');
        $this->listarProductosComercios = service('listarProductosComercios');
        $this->editarProducto = service('editarProducto');
        $this->cambiarEstadoProducto = service('cambiarEstadoProducto');
        $this->refreshToken = service('$refreshToken');
        $this->logoutUser = service('logoutUser');
        $this->consultarPedidosActivos = service('consultarPedidosActivos');
        $this->editarInventario = service('editarInventario');
        $this->consultarHistorialPedidos = service('consultarHistorialPedidos');
        $this->buscarComerciosPorFiltros = service('buscarComerciosPorFiltros');
        $this->editarPerfil = service('editarPerfil');
        $this->getPerfil = service('getPerfil');
    }
}