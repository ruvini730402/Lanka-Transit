<?php
/**
 * Search API Endpoint
 * 
 * Handles AJAX requests for bus search functionality
 */

// Include required files
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/controllers/SearchController.php';

// Initialize application
AppConfig::init();

// Set JSON response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Handle CORS if needed
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $allowedOrigins = [
        'http://localhost',
        'http://127.0.0.1',
        AppConfig::getBaseUrl()
    ];
    
    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Only allow GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Method not allowed', 405);
    }
    
    // Get action parameter
    $action = $_GET['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Action parameter is required', 400);
    }
    
    // Create controller instance
    $controller = new SearchController();
    
    // Route to appropriate method based on action
    switch ($action) {
        case 'search':
            $response = $controller->searchBuses();
            break;
            
        case 'origins':
            $response = $controller->getOrigins();
            break;
            
        case 'destinations':
            $response = $controller->getDestinations();
            break;
            
        default:
            throw new Exception('Invalid action specified', 400);
    }
    
    // Send response
    echo json_encode($response);
    
} catch (Exception $e) {
    // Log error
    error_log("API Error: " . $e->getMessage());
    
    // Send error response
    $errorCode = $e->getCode() ?: 500;
    http_response_code($errorCode);
    
    $errorResponse = [
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ];
    
    // Don't expose internal errors in production
    if (!AppConfig::isDevelopment()) {
        if ($errorCode >= 500) {
            $errorResponse['message'] = 'Internal server error. Please try again later.';
        }
    }
    
    echo json_encode($errorResponse);
}
