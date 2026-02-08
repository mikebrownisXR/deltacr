<?php
require_once __DIR__ . '/config.php';

function getPDO()
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        log_error('DB connection failed: ' . $e->getMessage());
        throw $e;
    }
}
