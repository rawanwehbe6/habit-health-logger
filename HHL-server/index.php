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

/**
 * Read JSON request body as array.
 *
 * @return array<string,mixed>
 */
function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
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
            serverError($e, 'Health check failed');
        }
        break;
    case 'login':
        if ($method !== 'POST') {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $body = getJsonBody();
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if ($email === '' || $password === '') {
            jsonResponse([
                'status' => 'error',
                'error'  => 'Email and password are required',
            ], 400);
        }

        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare(
                'SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1'
            );
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                jsonResponse([
                    'status' => 'error',
                    'error'  => 'Invalid email or password',
                ], 401);
            }

            jsonResponse([
                'status' => 'ok',
                'user'   => [
                    'id'    => $user['id'],
                    'email' => $user['email'],
                ],
            ]);
        } catch (Throwable $e) {
            serverError($e);
        }
        break;

    default:
        jsonResponse([
            'error'  => 'Not found',
            'action' => $action,
            'method' => $method,
        ], 404);
}

function serverError(Throwable $e, string $publicMessage = 'Server error'): void
{
    jsonResponse([
        'status'  => 'error',
        'error'   => $publicMessage,
        'message' => $e->getMessage(),
    ], 500);
}
