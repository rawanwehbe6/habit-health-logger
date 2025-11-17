<?php

declare(strict_types=1);

class HealthController
{
    public static function check(): void
    {
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
    }
}
