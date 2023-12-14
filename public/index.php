<?php

use Core\Response;
use Core\Session;
use Core\Settings;
use Core\ValidationException;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';

session_start();

require BASE_PATH . 'Core/functions.php';

require base_path('bootstrap.php');

$router = new \Core\Router;
$routes = require base_path('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$maintenance = new Settings('maintenance');

if ($maintenance->get('status') === 1) {
    $rutas_permitidas = $maintenance->get('allowed_routes');
    if (!in_array($uri, $rutas_permitidas, true)) {
        if (user() && !isSuperAdmin()) {
            http_response_code(Response::MAINTENANCE);
            require base_path("views/503.php");
            die();
        }
    }
}

try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {
    Session::flash('errors', $exception->errors);
    Session::flash('old', $exception->old);

    return redirect($router->previousUrl());
}

Session::unflash();
