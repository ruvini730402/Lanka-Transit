<?php

namespace App\Config;

class Database {
    private $host = 'bosennoy016fmb5flv0m-mysql.services.clever-cloud.com';
    private $db_name = 'bosennoy016fmb5flv0m';
    private $username = 'ul9ivik7jhoj9kyh';
    private $password = 'iVbsGABNeLEWyG69bSqj';
    private $conn;

    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection(): ?\PDO {
        if ($this->conn === null) {
            try {
                $this->conn = new \PDO(
                    "mysql:host={$this->host};dbname={$this->db_name}",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    ]
                );
            } catch (\PDOException $exception) {
                error_log("Connection error: " . $exception->getMessage());
                return null;
            }
        }
        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function closeConnection(): void {
        $this->conn = null;
    }

    /**
     * Execute a SELECT query and fetch all results
     * Includes connection handling and error logging
     *
     * @param string $query SQL query to execute
     * @param array $params Query parameters
     * @return array Query result or empty array on failure
     */
    public function fetchAllQuery(string $query, array $params = []): array {
        $connection = $this->getConnection();

        // Проверяем соединение
        if (!$connection) {
            error_log("Database connection is not available.");
            return [];
        }

        try {
            $stmt = $connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $exception) {
            error_log("Query execution error: " . $exception->getMessage());
            return [];
        }
    }

    /**
     * @param string $query
     * @param array $params
     * @return \PDOStatement|false
     */
    public function executeQuery(string $query, array $params = []): \PDOStatement|false
    {
        try {
            $stmt = $this->getConnection()?->prepare($query);
            $stmt?->execute($params);
            return $stmt;
        } catch (\PDOException $exception) {
            error_log("Query execution error: " . $exception->getMessage());
            return false;
        }
    }

    /**
     * Sanitize input to prevent XSS attacks
     */
    public static function sanitizeInput(string $data): string
    {
        return htmlspecialchars(trim(stripslashes($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate input data
     */
    public static function validateInput(string $data, string $type = 'string'): bool
    {
        return match ($type) {
            'email' => filter_var($data, FILTER_VALIDATE_EMAIL) !== false,
            'phone' => preg_match('/^[0-9]{10}$/', $data) === 1,
            'date' => strtotime($data) !== false,
            'number' => is_numeric($data),
            default => !empty(trim($data)),
        };
    }

}