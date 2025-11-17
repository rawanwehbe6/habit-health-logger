<?php

declare(strict_types=1);

require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/lib/http.php';

$method = $_SERVER['REQUEST_METHOD'];

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
