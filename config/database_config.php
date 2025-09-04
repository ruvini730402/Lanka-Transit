<?php
/**
 * Database Configuration
 * Contains database connection credentials and settings
 */

class DatabaseConfig {
    // Database connection credentials
    const DB_HOST = 'bosennoy016fmb5flv0m-mysql.services.clever-cloud.com';
    const DB_NAME = 'bosennoy016fmb5flv0m';
    const DB_USERNAME = 'ul9ivik7jhoj9kyh';
    const DB_PASSWORD = 'iVbsGABNeLEWyG69bSqj';
    
    // Database connection options
    const DB_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ];
    
    /**
     * Get database DSN (Data Source Name)
     * @return string
     */
    public static function getDSN() {
        return "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME;
    }
    
    /**
     * Get database host
     * @return string
     */
    public static function getHost() {
        return self::DB_HOST;
    }
    
    /**
     * Get database name
     * @return string
     */
    public static function getDatabaseName() {
        return self::DB_NAME;
    }
    
    /**
     * Get database username
     * @return string
     */
    public static function getUsername() {
        return self::DB_USERNAME;
    }
    
    /**
     * Get database password
     * @return string
     */
    public static function getPassword() {
        return self::DB_PASSWORD;
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
