<?php

namespace App\Utiliter\Foundation;

use PDO;
use PDOException;
use PDOStatement;

class Database {

    private PDO $pdo;

    /**
     * Initialize connection with database.
     * @throws PDOException
     */
    public function __construct()
    {
        [
            $user,
            $pass,
            $host,
            $port,
            $database
        ] = $this->getEnvSettings();

        try {
            $this->pdo = new PDO(
                $this->buildDsn($host, $port, $database),
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Based on current runtime enviroment get the connection details.
     * @return array<string|null>
     */
    private function getEnvSettings(): array
    {
        $prefix = (bool) env('testing') ? 'TEST_' : '';

        return [
            env("{$prefix}DB_USER", 'utiliter_admin'),
            env("{$prefix}DB_PASS", 'admin'),
            env("{$prefix}DB_HOST", 'mysql'),
            env("{$prefix}DB_PORT", '3306'),
            env("{$prefix}DB_NAME", 'utiliter')
        ];
    }

    /**
     * Build connection string.
     * @param string $host
     * @param string $port
     * @param string $database
     * @return string
     */
    private function buildDsn(string $host, string $port, string $database): string
    {
        return sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $database
        );
    }

    /**
     * Connection instance.
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Build and execute the query statement.
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    private function prepareExecute(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
 
    /**
     * Execute query and return the result set.
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->prepareExecute($sql, $params)->fetchAll() ?? [];
    }
 
    /**
     * Execute query and return single result.
     * @param string $sql
     * @param array $params
     * @return object|false
     */
    public function fetch(string $sql, array $params = []): object|false
    {
        return $this->prepareExecute($sql, $params)->fetch();
    }
 
    /**
     * Execute DML statements (INSERT, UPDATE, DELETE).
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->prepareExecute($sql, $params);
 
        $id = $this->pdo->lastInsertId();
        return $id ? (int) $id : $stmt->rowCount();
    }

    /**
     * Check if table has record.
     * @param string $table
     * @param string $column
     * @param mixed $value
     * @return bool
     */
    public function has(string $table, string $column, mixed $value): bool
    {
        return $this->fetch(
            "SELECT id FROM {$table} WHERE {$column} = ? LIMIT 1",
            [$value]
        ) !== false;
    }

    /**
     * Before inserting products, fetch existing categories/manufacturers
     * from database with its title/id.
     * @param string $table
     * @param string $column
     * @return array
     */
    public function buildMap(string $table, string $column = 'name'): array
    {
        $rows = $this->fetchAll("SELECT id, {$column} FROM {$table}");
        $map = [];
        foreach ($rows as $row) {
            $map[$row->naziv] = $row->id;
        }
        return $map;
    }

}