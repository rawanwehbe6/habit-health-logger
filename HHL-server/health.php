<?php

declare(strict_types=1);

require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/lib/http.php';

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
