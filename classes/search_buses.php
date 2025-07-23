<?php
/**
 * Search Buses API Endpoint
 * Returns available buses based on route and date
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once 'Bus.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit();
}

// Validate required fields
$required_fields = ['origin', 'destination', 'travel_date'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        echo json_encode(['error' => "Missing required field: $field"]);
        exit();
    }
}

try {
    // Initialize database and bus object
    $database = new Database();
    $db = $database->getConnection();
    $bus = new Bus($db);
    
    // Search for buses
    $result = $bus->searchBuses(
        $input['origin'],
        $input['destination'],
        $input['travel_date'],
        isset($input['max_fare']) ? $input['max_fare'] : null
    );
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Bus search API error: " . $e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
}
?>
