<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Base Model — menyediakan PDO wrapper dan query helpers.
 */
abstract class Model
{
    protected static ?PDO $pdo = null;

    /**
     * Get the PDO instance (singleton via Connection).
     */
    protected static function db(): PDO
    {
        if (static::$pdo === null) {
            static::$pdo = \App\Database\Connection::getInstance();
        }
        return static::$pdo;
    }

    /**
     * Run a SELECT query and return all rows.
     */
    protected static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Run a SELECT query and return a single row.
     */
    protected static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Run an INSERT/UPDATE/DELETE query and return affected rows.
     */
    protected static function execute(string $sql, array $params = []): int
    {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Run INSERT and return the last inserted ID.
     */
    protected static function insert(string $sql, array $params = []): string
    {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return static::db()->lastInsertId();
    }
}
