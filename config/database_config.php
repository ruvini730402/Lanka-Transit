<?php
/**
 * Database Configuration
 * Uses environment variables for secure credential management
 * 
 * Configuration Priority:
 * 1. Environment Variables (Production)
 * 2. .env file (Development)
 * 3. Local config files (Local development)
 */

require_once __DIR__ . '/env_loader.php';

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
        $host = EnvLoader::get('DB_HOST');
        if (empty($host)) {
            throw new Exception('DB_HOST environment variable is required');
        }
        return $host;
    }
    
    /**
     * Get database name
     * @return string
     */
    public static function getDatabaseName() {
        $dbName = EnvLoader::get('DB_NAME');
        if (empty($dbName)) {
            throw new Exception('DB_NAME environment variable is required');
        }
        return $dbName;
    }
    
    /**
     * Get database username
     * @return string
     */
    public static function getUsername() {
        $username = EnvLoader::get('DB_USERNAME');
        if (empty($username)) {
            throw new Exception('DB_USERNAME environment variable is required');
        }
        return $username;
    }
    
    /**
     * Get database password
     * @return string
     */
    public static function getPassword() {
        // Password can be empty for some local setups, so we don't throw an error
        return EnvLoader::get('DB_PASSWORD', '');
    }
    
    /**
     * Get database charset
     * @return string
     */
    public static function getCharset() {
        return EnvLoader::get('DB_CHARSET', 'utf8mb4');
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
}
?>
