<?php
/**
 * Session Configuration for Production Environment
 * This file sets up proper session handling for shared hosting
 */

// Configure session settings for production environment
if (!session_id()) {
    // Set session cookie parameters for security and compatibility
    session_set_cookie_params([
        'lifetime' => 3600, // 1 hour
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Set session save path to a writable directory
    $session_path = __DIR__ . '/../tmp/sessions';
    if (!is_dir($session_path)) {
        @mkdir($session_path, 0755, true);
    }
    
    if (is_dir($session_path) && is_writable($session_path)) {
        session_save_path($session_path);
    }
    
    // Configure session name and other settings
    session_name('LANKATRANSIT_SESSION');
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_lifetime', 3600);
    
    // Start the session
    session_start();
    
    // Regenerate session ID for security (but not on every request to avoid issues)
    if (!isset($_SESSION['regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['regenerated'] = time();
    } elseif (time() - $_SESSION['regenerated'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['regenerated'] = time();
    }
}
?>