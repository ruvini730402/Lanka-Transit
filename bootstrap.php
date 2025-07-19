<?php
/**
 * Application Autoloader
 * 
 * Simple autoloader for the application classes
 */

// Include configuration files
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Initialize application
AppConfig::init();

/**
 * Simple autoloader function
 */
spl_autoload_register(function ($className) {
    // Define the base directories for different types of classes
    $directories = [
        __DIR__ . '/src/models/',
        __DIR__ . '/src/controllers/',
        __DIR__ . '/src/utils/',
        __DIR__ . '/src/views/'
    ];
    
    // Try to find and include the class file
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Error handler function
 */
function customErrorHandler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    $errorMessage = "Error: {$message} in {$file} on line {$line}";
    error_log($errorMessage);
    
    if (AppConfig::isDevelopment()) {
        echo "<div style='background: #ffebee; color: #c62828; padding: 10px; margin: 10px; border-left: 4px solid #c62828;'>";
        echo "<strong>Error:</strong> {$message}<br>";
        echo "<strong>File:</strong> {$file}<br>";
        echo "<strong>Line:</strong> {$line}";
        echo "</div>";
    }
}

set_error_handler('customErrorHandler');

/**
 * Exception handler function
 */
function customExceptionHandler($exception) {
    $errorMessage = "Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($errorMessage);
    
    if (AppConfig::isDevelopment()) {
        echo "<div style='background: #ffebee; color: #c62828; padding: 10px; margin: 10px; border-left: 4px solid #c62828;'>";
        echo "<strong>Uncaught Exception:</strong> " . htmlspecialchars($exception->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "<strong>Stack Trace:</strong><br><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        echo "<h1>Something went wrong</h1>";
        echo "<p>We're sorry, but something went wrong. Please try again later.</p>";
    }
}

set_exception_handler('customExceptionHandler');
