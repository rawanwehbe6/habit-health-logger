<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/pdo.php';

/**
 * Send a JSON response and exit.
 *
 * @param mixed 
 * @param int   
 */
function jsonResponse($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'health':
        // verify DB connectivity
        try {
            $pdo = getPDO();
            $pdo->query('SELECT 1');
            jsonResponse([
                'status' => 'ok',
                'db'     => 'connected',
                'time'   => date('c'),
            ]);
        } catch (Throwable $e) {
            jsonResponse([
                'status'  => 'error',
                'db'      => 'failed',
                'message' => $e->getMessage(),
            ], 500);
        }
        break;

    default:
        jsonResponse([
            'error'  => 'Not found',
            'action' => $action,
            'method' => $method,
        ], 404);
}
