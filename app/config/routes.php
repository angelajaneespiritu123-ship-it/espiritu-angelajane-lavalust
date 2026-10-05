<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/** @var object $router **/

$router->get('/', 'Welcome::index');

$router->get('/login', 'AuthController::login');
$router->post('/login', 'AuthController::authenticate');
$router->get('/register', 'AuthController::register');
$router->post('/register', 'AuthController::store_register');
$router->get('/logout', 'AuthController::logout');

$router->get('/products', 'ProductController::index');
$router->get('/products/create', 'ProductController::create');
$router->post('/products/store', 'ProductController::store');
$router->get('/products/edit/{id}', 'ProductController::edit')->where_number('id');
$router->post('/products/update/{id}', 'ProductController::update')->where_number('id');
$router->get('/products/delete/{id}', 'ProductController::delete')->where_number('id');

$router->get('/student', 'StudentController::index');
$router->get('/student/profile', 'StudentController::profile');

$router->get('/users', 'UsersController::index');
$router->get('/show-users', 'UsersController::index');
$router->post('/api/login', 'ApiAuthController::login');
$router->post('/api/register', 'ApiAuthController::register');
$router->get('/api/products', 'ApiProductController::index');
$router->post('/api/products', 'ApiProductController::store');
$router->put('/api/products/{id}', 'ApiProductController::update')->where_number('id');
$router->delete('/api/products/{id}', 'ApiProductController::delete')->where_number('id');

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = array_values(array_filter(array_map(
        'trim',
        explode(',', getenv('FRONTEND_ORIGINS') ?: 'http://localhost:5173')
    )));

    if (in_array($origin, $allowedOrigins, true)) {
        header("Access-Control-Allow-Origin: {$origin}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, X-RateLimit-*');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
        header('Access-Control-Max-Age: 3600');
        header('Vary: Origin');
        http_response_code(204);
        exit;
    }
}