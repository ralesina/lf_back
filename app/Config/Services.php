<?php
namespace Config;

use CodeIgniter\Config\BaseService;

// Importaciones de Interfaces
use App\Domains\Auth\Repositories\IAuthRepository;
use App\Domains\Clientes\Repositories\{IClienteRepository, IPedidoRepository};
use App\Domains\Comercios\Repositories\{IComercioRepository, IProductoRepository};

// Importaciones de Implementaciones de Repositorios
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
use App\Domains\Comercios\Services\{
    ComercioService,
    ProductoService
};
use App\Infrastructure\Services\UploadService;

// Importaciones de Casos de Uso - Auth
use App\Application\UsesCases\Auth\{EditarPerfil, GetPerfil, RegisterUser, AuthenticateUser, LogoutUser, RefreshToken};

// Importaciones de Casos de Uso - Clientes
use App\Application\UsesCases\Clientes\{ListarProductosComercio,
    RealizarPedido,
    ConsultarPedidoDetalle,
    CancelarPedido,
    ConsultarHistorialPedidos,
    ConsultarPedidosActivos,
    BuscarComerciosPorFiltros};

// Importaciones de Casos de Uso - Comercios
use App\Application\UsesCases\Comercios\{
    BuscarComercio,
    BuscarCercano,
    GestionarPedido,
    EditarInventario,
    AgregarProducto,
    BuscarComercios,
    CambiarEstadoProducto,
    EditarProducto,
    ListarCategorias,
    ListarComerciosDestacados,
    ListarPedidosComercio,
    ListarProductosComercios
};

class Services extends BaseService
{
    // Repositories
    public static function authRepository($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('authRepository');
        return new AuthRepository();
    }

    public static function clienteRepository($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('clienteRepository');
        return new ClienteRepository();
    }

    public static function pedidoRepository($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('pedidoRepository');
        return new PedidoRepository();
    }

    public static function comercioRepository($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('comercioRepository');
        return new ComercioRepository();
    }

    public static function productoRepository($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('productoRepository');
        return new ProductoRepository();
    }

    // Services
    public static function uploadService($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('uploadService');
        return new UploadService();
    }

    public static function authService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authService');
        }

        return new \App\Domains\Auth\Services\AuthService(
            static::authRepository(),
            new \App\Infrastructure\Auth\JWTService()
        );
    }
    public static function consultarPedidoDetalle($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('consultarPedidoDetalle');
        return new ConsultarPedidoDetalle(static::pedidoService());
    }
    public static function pedidoService($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('pedidoService');
        return new PedidoService(static::pedidoRepository(), static::productoRepository());
    }

    public static function comercioService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('comercioService');
        }
        return new ComercioService(
            static::comercioRepository(),
            static::uploadService()  // Cambiado el orden de las dependencias
        );
    }

    public static function productoService($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('productoService');
        return new ProductoService(static::productoRepository());
    }

    // Use Cases - Auth
    public static function registerUser($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('registerUser');
        return new RegisterUser(static::authService());
    }

    public static function authenticateUser($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('authenticateUser');
        return new AuthenticateUser(static::authService());
    }

    public static function refreshToken($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('refreshToken');
        return new RefreshToken(static::authService());
    }

    public static function logoutUser($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('logoutUser');
        return new LogoutUser(static::authService());
    }
    public static function getPerfil($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('getPerfil');
        return new GetPerfil(static::authService());
    }

    public static function editarPerfil($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('editarPerfil');
        return new EditarPerfil(static::authService());
    }
    // Use Cases - Clientes
    public static function realizarPedido($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('realizarPedido');
        return new RealizarPedido(
            static::pedidoService(),
            static::clienteRepository()
        );
    }

    public static function consultarPedidos($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('consultarPedidos');
        return new ConsultarPedidoDetalle(static::pedidoService());
    }
    public static function consultarPedido($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('consultarPedidos');
        return new ConsultarPedidoDetalle(static::pedidoService());
    }
    public static function cancelarPedido($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('cancelarPedido');
        return new CancelarPedido(static::pedidoRepository());
    }

    public static function consultarHistorialPedidos($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('consultarHistorialPedidos');
        return new ConsultarHistorialPedidos(static::pedidoService());
    }

    public static function consultarPedidosActivos($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('consultarPedidosActivos');
        return new ConsultarPedidosActivos(static::pedidoService());
    }

    public static function buscarComerciosPorFiltros($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('buscarComerciosPorFiltros');
        return new BuscarComerciosPorFiltros(static::comercioService());
    }

    // Use Cases - Comercios
    public static function buscarComercio($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('buscarComercio');
        return new BuscarComercio(static::comercioRepository());
    }

    public static function buscarCercano($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('buscarCercano');
        return new BuscarCercano(static::comercioService());
    }

    public static function gestionarPedido($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('gestionarPedido');
        return new GestionarPedido(static::pedidoRepository());
    }

    public static function editarInventario($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('editarInventario');
        return new EditarInventario(static::productoRepository());
    }

    public static function agregarProducto($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('agregarProducto');
        }
        return new AgregarProducto(
            static::comercioService(),
            static::comercioRepository()  // Agregada la dependencia faltante
        );
    }

    public static function editarProducto($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('editarProducto');
        }
        return new EditarProducto(static::productoRepository(), static::uploadService());

    }

    public static function cambiarEstadoProducto($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('cambiarEstadoProducto');
        return new CambiarEstadoProducto(static::productoRepository());
    }

    public static function listarCategorias($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('listarCategorias');
        return new ListarCategorias(static::comercioRepository());
    }

    public static function listarComerciosDestacados($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('listarComerciosDestacados');
        return new ListarComerciosDestacados(static::comercioRepository());
    }

    public static function listarProductosComercios($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('listarProductosComercios');
        }
        return new ListarProductosComercios(
            static::productoRepository(),
            static::comercioRepository()
        );
    }
    public static function listarProductosComercio($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('listarProductosComercio');
        }
        return new ListarProductosComercio(
            static::productoService());
    }
    public static function listarPedidosComercio($getShared = true)
    {
        if ($getShared) return static::getSharedInstance('listarPedidosComercio');
        return new ListarPedidosComercio(static::pedidoRepository(), static::comercioRepository());
    }
}