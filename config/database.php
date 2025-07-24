<?php
/**
 * Database Configuration and Connection Class
 * Handles database connection with proper error handling and security
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'transit';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            return null;
        }
        
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
    
    /**
     * Execute a prepared statement with parameters
     * @param string $query
     * @param array $params
     * @return PDOStatement|false
     */
    public function executeQuery($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $exception) {
            error_log("Query error: " . $exception->getMessage());
            return false;
        }
    }
    
    /**
     * Sanitize input to prevent XSS attacks
     * @param string $data
     * @return string
     */
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Validate input data
     * @param string $data
     * @param string $type
     * @return bool
     */
    public static function validateInput($data, $type = 'string') {
        switch($type) {
            case 'email':
                return filter_var($data, FILTER_VALIDATE_EMAIL);
            case 'phone':
                return preg_match('/^[0-9]{10}$/', $data);
            case 'date':
                return (bool)strtotime($data);
            case 'number':
                return is_numeric($data);
            default:
                return !empty(trim($data));
        }
    }
}
?>