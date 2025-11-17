<?php

declare(strict_types=1);

class User
{
    public int $id;
    public string $email;
    public string $passwordHash;

    public function __construct(int $id, string $email, string $passwordHash)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public static function findByEmail(PDO $pdo, string $email): ?User
    {
        $stmt = $pdo->prepare(
            'SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new User(
            (int) $row['id'],
            (string) $row['email'],
            (string) $row['password_hash']
        );
    }
}
