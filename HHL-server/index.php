<?php

declare(strict_types=1);

require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/lib/http.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Services/AuthService.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/HealthController.php';

$route  = $_GET['route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($route) {
    case 'health':
        HealthController::check();
        break;

    case 'login':
        AuthController::login();
        break;

    default:
        jsonResponse([
            'error' => 'Not found',
            'route' => $route,
            'method' => $method,
        ], 404);
}
