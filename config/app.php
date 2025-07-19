<?php
/**
 * Application Configuration
 * 
 * Global application settings and constants
 */

class AppConfig {
    // Application settings
    public const APP_NAME = 'Lanka Transit';
    public const APP_VERSION = '1.0.0';
    public const APP_URL = 'http://localhost/lanka-transit';
    
    // Security settings
    public const SESSION_TIMEOUT = 3600; // 1 hour
    public const MAX_LOGIN_ATTEMPTS = 5;
    public const CSRF_TOKEN_EXPIRE = 1800; // 30 minutes
    
    // Pagination
    public const ITEMS_PER_PAGE = 10;
    public const MAX_ITEMS_PER_PAGE = 100;
    
    // File upload settings
    public const MAX_FILE_SIZE = 5242880; // 5MB
    public const ALLOWED_IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'gif'];
    
    // Time zone
    public const DEFAULT_TIMEZONE = 'Asia/Colombo';
    
    // Date formats
    public const DATE_FORMAT = 'Y-m-d';
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';
    public const DISPLAY_DATE_FORMAT = 'd/m/Y';
    public const DISPLAY_DATETIME_FORMAT = 'd/m/Y H:i';
    
    /**
     * Initialize application configuration
     */
    public static function init(): void {
        // Set timezone
        date_default_timezone_set(self::DEFAULT_TIMEZONE);
        
        // Set error reporting based on environment
        if (self::isDevelopment()) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if application is in development mode
     * 
     * @return bool
     */
    public static function isDevelopment(): bool {
        return isset($_SERVER['HTTP_HOST']) && 
               (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
    }
    
    /**
     * Get base URL
     * 
     * @return string
     */
    public static function getBaseUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        
        return $protocol . '://' . $host . $path;
    }
}
