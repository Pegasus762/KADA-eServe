<?php
session_start();

// Basic Router
$uri = trim($_SERVER['REQUEST_URI'], '/');
$method = $_SERVER['REQUEST_METHOD'];

// Public routes that don't require authentication
$public_routes = ['login', 'register', 'logout'];

// Check if the route requires authentication
if (!in_array($uri, $public_routes) && !isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';
require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/core/Autoload.php';

use App\Controllers\UserController;

$controller = new UserController();

if ($uri === 'login' && $method === 'GET') {
    $controller->loginForm();
} elseif ($uri === 'login' && $method === 'POST') {
    $controller->login();
} elseif ($uri === 'register' && $method === 'GET') {
    $controller->register();
} elseif ($uri === 'register' && $method === 'POST') {
    $controller->register();
} elseif ($uri === 'logout') {
    $controller->logout();
} elseif ($uri === '' && $method === 'GET') {
    $controller->index();
} elseif ($uri === 'create' && $method === 'GET') {
    $controller->create();
} elseif ($uri === 'store' && $method === 'POST') {
    $controller->store();
} elseif (preg_match('/edit\/(\d+)/', $uri, $matches) && $method === 'GET') {
    $controller->edit($matches[1]);
} elseif (preg_match('/update\/(\d+)/', $uri, $matches) && $method === 'POST') {
    $controller->update($matches[1]);
} elseif (preg_match('/delete\/(\d+)/', $uri, $matches) && $method === 'POST') {
    $controller->delete($matches[1]);
} else {
    http_response_code(404);
    echo "Page not found.";
}
