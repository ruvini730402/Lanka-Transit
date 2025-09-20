<?php
/**
 * Environment Loader - Handles multiple configuration sources
 * Priority: Environment Variables > .env file > local config files
 * 
 * This class automatically loads environment variables from multiple sources
 * and provides a unified interface for configuration management.
 */
class EnvLoader {
    private static $loaded = false;
    
    /**
     * Load environment variables from all available sources
     */
    public static function load() {
        if (self::$loaded) {
            return;
        }
        
        // Load from .env file if exists (lower priority)
        self::loadFromFile(__DIR__ . '/../.env');
        
        // Load from local config if exists (higher priority)
        self::loadFromFile(__DIR__ . '/../.env.local');
        
        // Load from production environment file if exists (highest priority for env files)
        self::loadFromFile(__DIR__ . '/../.env.production');
        
        self::$loaded = true;
    }
    
    /**
     * Load environment variables from a specific file
     * @param string $filepath Path to the environment file
     */
    private static function loadFromFile($filepath) {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            return;
        }
        
        $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }
            
            // Parse key=value pairs
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove surrounding quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            // Only set if not already set (preserves higher priority sources)
            if (!isset($_ENV[$key]) && !getenv($key)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    /**
     * Get an environment variable with optional default value
     * @param string $key Environment variable key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get($key, $default = null) {
        self::load();
        
        // Check $_ENV first, then getenv(), then return default
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Check if an environment variable exists
     * @param string $key Environment variable key
     * @return bool
     */
    public static function has($key) {
        self::load();
        return isset($_ENV[$key]) || getenv($key) !== false;
    }
    
    /**
     * Get all environment variables as an array
     * @return array
     */
    public static function all() {
        self::load();
        return $_ENV;
    }
    
    /**
     * Set an environment variable (for testing purposes)
     * @param string $key Environment variable key
     * @param mixed $value Environment variable value
     */
    public static function set($key, $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
    
    /**
     * Get database connection string
     * @return string
     */
    public static function getDatabaseUrl() {
        $host = self::get('DB_HOST', 'localhost');
        $port = self::get('DB_PORT', '3306');
        $database = self::get('DB_NAME');
        $username = self::get('DB_USERNAME');
        $password = self::get('DB_PASSWORD');
        
        if (!$database || !$username) {
            return null;
        }
        
        return "mysql://{$username}:{$password}@{$host}:{$port}/{$database}";
    }
    
    /**
     * Check if we're in production environment
     * @return bool
     */
    public static function isProduction() {
        $env = self::get('APP_ENV', 'development');
        return in_array(strtolower($env), ['production', 'prod']);
    }
    
    /**
     * Check if debug mode is enabled
     * @return bool
     */
    public static function isDebug() {
        $debug = self::get('DEBUG', 'false');
        return filter_var($debug, FILTER_VALIDATE_BOOLEAN);
    }
}

// Auto-load environment variables when this file is included
EnvLoader::load();
?>