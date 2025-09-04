<?php
// Debug script to test database connection and classes
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Lanka Transit Components...\n";

// Test 1: Check if files exist
echo "1. Checking file existence:\n";
$files = [
    'classes/Database.php',
    'classes/Payment.php',
    'config/database_config.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file exists\n";
    } else {
        echo "   ✗ $file missing\n";
    }
}

// Test 2: Try to include classes
echo "\n2. Testing class inclusion:\n";
try {
    require_once 'classes/Database.php';
    echo "   ✓ Database class included\n";
} catch (Exception $e) {
    echo "   ✗ Database class error: " . $e->getMessage() . "\n";
}

try {
    require_once 'classes/Payment.php';
    echo "   ✓ Payment class included\n";
} catch (Exception $e) {
    echo "   ✗ Payment class error: " . $e->getMessage() . "\n";
}

// Test 3: Try database connection
echo "\n3. Testing database connection:\n";
try {
    $db = new Database();
    $conn = $db->getConnection();
    if ($conn) {
        echo "   ✓ Database connection successful\n";
        
        // Test query
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM User LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   ✓ Database query successful - User count: " . $result['count'] . "\n";
        
    } else {
        echo "   ✗ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Test 4: Test Payment class
echo "\n4. Testing Payment class:\n";
try {
    $payment = new Payment();
    echo "   ✓ Payment class instantiated\n";
} catch (Exception $e) {
    echo "   ✗ Payment class error: " . $e->getMessage() . "\n";
}

echo "\nDebug test completed.\n";
?>
