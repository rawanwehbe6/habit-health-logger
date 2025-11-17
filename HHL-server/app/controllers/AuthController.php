<?php

declare(strict_types=1);

class AuthController
{
    public static function login(): void
    {
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

            $user = AuthService::login($pdo, $email, $password);

            if ($user === null) {
                jsonResponse([
                    'status' => 'error',
                    'error'  => 'Invalid email or password',
                ], 401);
            }

            jsonResponse([
                'status' => 'ok',
                'user'   => [
                    'id'    => $user->id,
                    'email' => $user->email,
                ],
            ]);
        } catch (Throwable $e) {
            serverError($e);
        }
    }
}
