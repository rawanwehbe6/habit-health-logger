<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';

class AuthService
{
    public static function login(PDO $pdo, string $email, string $password): ?User
    {
        $user = User::findByEmail($pdo, $email);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->passwordHash)) {
            return null;
        }

        return $user;
    }
}
