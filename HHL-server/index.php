<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/http.php';

jsonResponse([
    'name'    => 'Habit & Health Logger API',
    'endpoints' => [
        'POST /HHL-server/login.php'  => 'Login with email and password',
        'GET  /HHL-server/health.php' => 'Health check',
    ],
]);
