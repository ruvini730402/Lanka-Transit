<?php
/**
 * Security Utility Class
 * 
 * Handles security-related operations like XSS prevention, CSRF protection, etc.
 */

class Security {
    
    /**
     * Sanitize input to prevent XSS attacks
     * 
     * @param mixed $input
     * @return mixed
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // Remove null bytes
            $input = str_replace(chr(0), '', $input);
            
            // Trim whitespace
            $input = trim($input);
            
            // Convert special characters to HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        return $input;
    }
    
    /**
     * Sanitize output for display
     * 
     * @param string $output
     * @return string
     */
    public static function sanitizeOutput(string $output): string {
        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Generate CSRF token
     * 
     * @return string
     */
    public static function generateCSRFToken(): string {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) ||
            (time() - $_SESSION['csrf_token_time']) > AppConfig::CSRF_TOKEN_EXPIRE) {
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * 
     * @param string $token
     * @return bool
     */
    public static function validateCSRFToken(string $token): bool {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Check token expiration
        if ((time() - $_SESSION['csrf_token_time']) > AppConfig::CSRF_TOKEN_EXPIRE) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Hash password securely
     * 
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    /**
     * Verify password
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate secure random token
     * 
     * @param int $length
     * @return string
     */
    public static function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Validate and sanitize email
     * 
     * @param string $email
     * @return string|false
     */
    public static function validateEmail(string $email) {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        return $email ? self::sanitizeInput($email) : false;
    }
    
    /**
     * Rate limiting check
     * 
     * @param string $identifier
     * @param int $max_attempts
     * @param int $time_window
     * @return bool
     */
    public static function checkRateLimit(string $identifier, int $max_attempts = 5, int $time_window = 900): bool {
        $key = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 1,
                'reset_time' => time() + $time_window
            ];
            return true;
        }
        
        // Reset if time window has passed
        if (time() > $_SESSION[$key]['reset_time']) {
            $_SESSION[$key] = [
                'attempts' => 1,
                'reset_time' => time() + $time_window
            ];
            return true;
        }
        
        // Check if limit exceeded
        if ($_SESSION[$key]['attempts'] >= $max_attempts) {
            return false;
        }
        
        $_SESSION[$key]['attempts']++;
        return true;
    }
}
