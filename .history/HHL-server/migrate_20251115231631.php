<?php

$config = require __DIR__ . '/config/db.php';

$host    = $config['host'];
$port    = $config['port'];
$dbName  = $config['dbname'];
$user    = $config['user'];
$pass    = $config['password'];
$charset = $config['charset'];

try {

    $dsnNoDb = "mysql:host=$host;port=$port;charset=$charset";
    $pdo = new PDO($dsnNoDb, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // 2) create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET $charset COLLATE utf8mb4_general_ci");

    // 3) switch to that database
    $pdo->exec("USE `$dbName`");

    // 4) create migrations table if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // 5) get already-applied migrations
    $stmt = $pdo->query("SELECT migration FROM migrations");
    $applied = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    // 6) run all .sql files in /migrations that are not applied
    $files = glob(__DIR__ . '/migrations/*.sql');
    sort($files);

    foreach ($files as $file) {
        $name = basename($file);

        if (in_array($name, $applied, true)) {
            continue; // already applied
        }

        $sql = file_get_contents($file);
        if (trim($sql) === '') {
            continue;
        }

        $pdo->exec($sql);

        $insert = $pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        $insert->execute(['migration' => $name]);

        echo "Applied migration: $name<br>";
    }

    echo "All migrations up to date.";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Migration error: " . htmlspecialchars($e->getMessage());
}
