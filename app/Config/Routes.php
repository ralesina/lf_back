<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api', ['namespace' => 'App\Infrastructure\Http\Controllers'], function($routes) {

    // Auth routes
    $routes->group('auth', function($routes) {
        $routes->post('login', 'ApiController::login');
        $routes->post('register', 'ApiController::register');
        $routes->post('refresh', 'ApiController::refreshToken');
    });

    // Cliente routes
    $routes->group('clientes', ['filter' => 'jwt-auth'], function($routes) {


        $routes->get('comercios', 'ApiController::getComercios');
        $routes->get('comercios/(:num)', 'ApiController::getDatosComercio/$1');

        $routes->get('comercios/cercanos', 'ApiController::buscarComerciosCercanos');
        $routes->get('comercios/destacados', 'ApiController::comerciosDestacados');
        $routes->get('comercios/(:num)/productos', 'ApiController::listarProductosComercio/$1');
        $routes->post('pedido', 'ApiController::realizarPedido');
        $routes->get('pedido/(:num)', 'ApiController::consultarPedidoDetalle/$1');
        $routes->get('pedidos/activos', 'ApiController::consultarPedidosActivos');
        $routes->get('pedidos/historial', 'ApiController::consultarHistorialPedidos');
        $routes->post('pedidos/(:num)/cancelar', 'ApiController::cancelarPedido/$1');
        $routes->get('categorias', 'ApiController::categorias');

    });

    // Comercio routes
    $routes->group('comercios', ['filter' => 'jwt-auth'], function($routes) {

        $routes->post('comercios/productos', 'ApiController::agregarProducto');
        $routes->get('comercios/productos', 'ApiController::listarProductos');
        $routes->post('comercios/inventario/(:num)', 'ApiController::editarInventario/$1');
        $routes->post('comercios/productos/(:num)', 'ApiController::editarProducto/$1');
        $routes->get('pedidos/(:segment)', 'ApiController::listarPedidosComercio/$1');
        $routes->get('pedidos', 'ApiController::listarPedidosComercio');
        $routes->put('pedidos/(:num)/estado', 'ApiController::gestionarPedido/$1');
        $routes->get('categorias', 'ApiController::categorias');

    });
    $routes->group('', ['filter' => 'jwt-auth'], function($routes) {
        $routes->get('perfil', 'ApiController::getPerfil');
        $routes->put('perfil', 'ApiController::editarPerfil');
    });
});

$routes->options('(:any)', 'BaseController::options');

return $routes;
