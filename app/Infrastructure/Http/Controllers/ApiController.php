<?php

namespace App\Infrastructure\Http\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\DomainException;
use App\Exceptions\ValidationException;
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
    public function __construct() {
        $this->inicializarServicios();

    }
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->inicializarServicios();
    }
    public function register()
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getJSON(true);

            if (empty($data)) {
                throw new \App\Exceptions\ValidationException([
                    'message' => 'No se recibieron datos'
                ]);
            }

            if (!isset($data['rol'])) {
                $data['rol'] = 'cliente';
            }

            return $this->registerUser->execute($data);
        });
    }

    public function login()
    {
        return $this->executeUseCase(function() {
            $data = $this->request->getJSON(true);

            if (!isset($data['email']) || !isset($data['password'])) {
                throw new \App\Exceptions\ValidationException([
                    'message' => 'Email y contraseña son requeridos'
                ]);
            }

            return $this->authenticateUser->execute(
                $data['email'],
                $data['password']
            );
        });
    }
    public function listarPedidosComercio()
    {
        return $this->executeUseCase(function() {
            $idUsuario = $this->request->user->id;

            if ($this->request->user->role !== 'comercio') {
                throw new \App\Exceptions\ValidationException([
                    'message' => 'No tiene permisos para realizar esta acción'
                ]);
            }

            return $this->listarPedidosComercio->execute($idUsuario);
        });
    }
    public function realizarPedido(RequestInterface $request): ResponseInterface
    {
        $data = $request->getJSON(true);
        $result = $this->realizarPedido->execute($data);
        return $this->respond(['success' => true, 'data' => $result]);
    }

    public function buscarComercio(int $id): ResponseInterface
    {
        $result = $this->buscarComercio->execute($id);
        return $this->respond(['success' => true, 'data' => $result]);
    }

    public function consultarPedidos(int $idCliente): ResponseInterface
    {
        return $this->executeUseCase(function() use ($idCliente) {
            return $this->consultarPedidos->execute($idCliente);
        });
    }

    public function buscarCercano(): ResponseInterface
    {
        $latitud = (float)$this->request->getGet('latitud');
        $longitud = (float)$this->request->getGet('longitud');
        $radio = (int)$this->request->getGet('radio');

        if(!$latitud || !$longitud || !$radio) {
            throw new ValidationException(['message' => 'Parámetros de búsqueda inválidos']);
        }

        return $this->executeUseCase(function() use ($latitud, $longitud, $radio) {
            return $this->buscarCercano->execute($latitud, $longitud, $radio);
        });
    }

    public function cancelarPedido(int $idPedido): ResponseInterface
    {
        $idCliente = $this->request->user->id;

        try {
            $this->cancelarPedido->execute($idPedido, $idCliente);
            return $this->respond(['success' => true, 'message' => 'Pedido cancelado exitosamente.']);
        } catch (ValidationException | DomainException $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Error interno del servidor.'], 500);
        }
    }

    public function gestionarPedido(int $idPedido): ResponseInterface
    {
        $idComercio = $this->request->user->id;
        $data = $this->request->getJSON(true);

        try {
            $this->gestionarPedido->execute($idPedido, $idComercio, $data['nuevoEstado']);
            return $this->respond(['success' => true, 'message' => 'Pedido actualizado exitosamente.']);
        } catch (ValidationException | DomainException $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Error interno del servidor.'], 500);
        }
    }
    public function agregarProducto(): ResponseInterface
    {
        return $this->executeUseCase(function() {
            if (!$this->request->user->role === 'comercio') {
                throw new ValidationException(['message' => 'No autorizado']);
            }

            $data = $this->request->getJSON(true);
            $data['id_comercio'] = $this->request->user->id;
            $image = $this->request->getFile('imagen');

            return $this->agregarProducto->execute($data, $image);
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
        $this->buscarComercio = service('buscarComercio');
        $this->buscarCercano = service('buscarCercano');
        $this->cancelarPedido = service('cancelarPedido');
        $this->gestionarPedido = service('gestionarPedido');
        $this->listarPedidosComercio = service('listarPedidosComercio');
        $this->agregarProducto = service('agregarProducto');
    }
}