<?php
/**
 * Database Configuration Template
 * This file is safe for GitHub. Copy to database_config.php and configure.
 * 
 * Configuration Priority:
 * 1. Environment Variables (Production)
 * 2. .env file (Development)
 * 3. Local config files (Local development)
 */
class DatabaseConfig {
    // Database connection credentials
    const DB_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ];
    
    /**
     * Get database host
     * @return string
     */
    public static function getHost() {
        return $_ENV['DB_HOST'] ?? self::getLocalConfig('host') ?? 'localhost';
    }
    
    /**
     * Get database name
     * @return string
     */
    public static function getDatabaseName() {
        return $_ENV['DB_NAME'] ?? self::getLocalConfig('database') ?? 'lanka_transit';
    }
    
    /**
     * Get database username
     * @return string
     */
    public static function getUsername() {
        return $_ENV['DB_USERNAME'] ?? self::getLocalConfig('username') ?? 'root';
    }
    
    /**
     * Get database password
     * @return string
     */
    public static function getPassword() {
        return $_ENV['DB_PASSWORD'] ?? self::getLocalConfig('password') ?? '';
    }
    
    /**
     * Get database charset
     * @return string
     */
    public static function getCharset() {
        return $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }
    
    /**
     * Get database DSN (Data Source Name)
     * @return string
     */
    public static function getDSN() {
        return "mysql:host=" . self::getHost() . ";dbname=" . self::getDatabaseName() . ";charset=" . self::getCharset();
    }
    
    /**
     * Get database connection options
     * @return array
     */
    public static function getOptions() {
        return self::DB_OPTIONS;
    }
    
    /**
     * Fallback to local config file if exists (not in repo)
     * @param string $key
     * @return mixed|null
     */
    private static function getLocalConfig($key) {
        $localConfigFile = __DIR__ . '/local_database_config.php';
        if (file_exists($localConfigFile)) {
            $config = include $localConfigFile;
            return $config[$key] ?? null;
        }
        return null;
    }
}
?>