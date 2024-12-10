<?php
namespace Config;

use CodeIgniter\Config\BaseService;

// Importaciones de Interfaces
use App\Domains\Auth\Repositories\IAuthRepository;
use App\Domains\Clientes\Repositories\{IClienteRepository, IPedidoRepository};
use App\Domains\Comercios\Repositories\{IComercioRepository, IProductoRepository};

// Importaciones de Implementaciones
use App\Infrastructure\Persistence\{
    AuthRepository,
    ClienteRepository,
    ComercioRepository,
    PedidoRepository,
    ProductoRepository
};

// Importaciones de Servicios
use App\Domains\Auth\Services\AuthService;
use App\Domains\Clientes\Services\{ClienteService, PedidoService};
use App\Domains\Comercios\Services\ComercioService;

// Importaciones de Casos de Uso
use App\Application\UsesCases\Auth\{RegisterUser, AuthenticateUser};
use App\Application\UsesCases\Clientes\{RealizarPedido, ConsultarPedido, CancelarPedido};
use App\Application\UsesCases\Comercios\{
    BuscarComercio,
    BuscarCercano,
    GestionarPedido,
    EditarInventario,
    AgregarProducto
};

class Services extends BaseService
{
    // Repositories
    public static function authRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authRepository');
        }
        return new AuthRepository();
    }

    public static function clienteRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('clienteRepository');
        }
        return new ClienteRepository();
    }

    public static function pedidoRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('pedidoRepository');
        }
        return new PedidoRepository();
    }

    public static function comercioRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('comercioRepository');
        }
        return new ComercioRepository();
    }

    public static function productoRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('productoRepository');
        }
        return new ProductoRepository();
    }

    // Services
    public static function authService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authService');
        }
        return new AuthService(static::authRepository());
    }

    public static function pedidoService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('pedidoService');
        }
        return new PedidoService(
            static::pedidoRepository(),
            static::productoRepository()
        );
    }

    public static function uploadService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('uploadService');
        }
        return new \App\Infrastructure\Services\UploadService();
    }

    public static function agregarProducto($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('agregarProducto');
        }

        return new \App\Application\UsesCases\Comercios\AgregarProducto(
            static::comercioService(),
        );
    }

    public static function comercioService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('comercioService');
        }

        return new \App\Domains\Comercios\Services\ComercioService(
            static::comercioRepository(),
            static::productoRepository(),
            static::uploadService()
        );
    }

    // Use Cases - Auth
    public static function registerUser($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('registerUser');
        }
        return new RegisterUser(static::authService());
    }

    public static function authenticateUser($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authenticateUser');
        }
        return new AuthenticateUser(static::authService());
    }

    // Use Cases - Clientes
    public static function realizarPedido($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('realizarPedido');
        }
        return new RealizarPedido(static::pedidoService());
    }

    public static function consultarPedidos($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('consultarPedidos');
        }
        return new ConsultarPedido(static::clienteRepository());
    }

    public static function cancelarPedido($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cancelarPedido');
        }
        return new CancelarPedido(static::pedidoRepository());
    }

    // Use Cases - Comercios
    public static function buscarComercio($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('buscarComercio');
        }
        return new BuscarComercio(static::comercioRepository());
    }

    public static function buscarCercano($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('buscarCercano');
        }
        return new BuscarCercano(static::comercioService());
    }

    public static function gestionarPedido($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('gestionarPedido');
        }
        return new GestionarPedido(static::pedidoRepository());
    }

    public static function editarInventario($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('editarInventario');
        }
        return new EditarInventario(static::productoRepository());
    }

    public static function listarPedidosComercio($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('listarPedidosComercio');
        }

        return new \App\Application\UsesCases\Comercios\ListarPedidosComercio(
            static::pedidoRepository(),
            static::comercioRepository()
        );
    }
}