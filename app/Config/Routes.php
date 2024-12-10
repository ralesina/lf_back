<?php
namespace Config;

use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */
$routes->group('auth', static function ($routes) {
    $routes->post('register', 'ApiController::register', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->post('login', 'ApiController::login', ['namespace' => 'App\Infrastructure\Http\Controllers']);
});

$routes->group('clientes', ['filter' => 'jwt-auth'], static function ($routes) {
    $routes->post('pedido', 'ApiController::realizarPedido', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->get('pedidos/(:num)', 'ApiController::consultarPedidos/$1', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->get('comercios/buscar/(:num)', 'ApiController::buscarComercio/$1', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->get('comercios/cercanos', 'ApiController::buscarCercano', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->post('pedido/cancelar/(:num)', 'ApiController::cancelarPedido/$1', ['namespace' => 'App\Infrastructure\Http\Controllers']);
});

$routes->group('comercios', ['filter' => 'jwt-auth'], static function ($routes) {
    $routes->post('inventario/(:num)', 'ApiController::editarInventario/$1', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->post('pedido/gestionar/(:num)', 'ApiController::gestionarPedido/$1', ['namespace' => 'App\Infrastructure\Http\Controllers']);
    $routes->get('pedidos', 'ApiController::listarPedidosComercio', ['namespace' => 'App\Infrastructure\Http\Controllers']);

});

$routes->options('(:any)', static function() {
    return '';
}, ['filter' => 'cors']);

return $routes;

