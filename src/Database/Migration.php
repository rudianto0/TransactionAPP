<?php

declare(strict_types=1);

namespace App\Database;

/**
 * Simple database migration runner.
 * Membaca file .sql dari folder migrations/ dan menjalankannya berurutan.
 */
class Migration
{
    private \PDO $pdo;
    private string $migrationsDir;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
        $this->migrationsDir = BASE_PATH . '/migrations';
    }

    /**
     * Run all pending migrations.
     */
    public function run(): void
    {
        // Ensure migrations tracking table exists
        $this->createMigrationsTable();

        // Get already-run migrations
        $executed = $this->getExecutedMigrations();

        // Get all .sql files sorted by name
        $files = glob($this->migrationsDir . '/*.sql');
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);

            if (in_array($filename, $executed, true)) {
                continue;
            }

            echo "  Running: {$filename} ... ";

            $sql = file_get_contents($file);
            $this->pdo->exec($sql);

            // Record migration
            $stmt = $this->pdo->prepare('INSERT INTO migrations (migration) VALUES (:name)');
            $stmt->execute(['name' => $filename]);

            echo "OK\n";
        }
    }

    /**
     * Create migrations tracking table if not exists.
     */
    private function createMigrationsTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Get list of already executed migration names.
     */
    private function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query('SELECT migration FROM migrations');
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
