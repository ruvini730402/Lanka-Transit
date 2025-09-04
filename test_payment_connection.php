<?php
/**
 * Test script to verify payment system database connections are working
 */

echo "Testing Payment System Database Connections...\n\n";

// Test config/database.php connection
echo "1. Testing config/database.php connection:\n";
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "✅ Config database connection successful\n";
        
        // Test Payment class initialization
        echo "\n2. Testing Payment class with config database:\n";
        require_once 'classes/Payment.php';
        
        $payment = new Payment($pdo);
        echo "✅ Payment class initialized successfully with config database\n";
        
    } else {
        echo "❌ Config database connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test classes/database[WRONG].php connection  
echo "\n3. Testing classes/database[WRONG].php connection:\n";
require_once 'classes/database[WRONG].php';

try {
    $pdo2 = Database::getConnection();
    
    if ($pdo2) {
        echo "✅ Classes database[WRONG] connection successful\n";
        
        // Test User class
        echo "\n4. Testing User class with classes database:\n";
        require_once 'classes/User.php';
        
        $user = new User();
        echo "✅ User class initialized successfully with classes database\n";
        
    } else {
        echo "❌ Classes database[WRONG] connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Payment system should now use config/database.php\n";
echo "User/Auth system should use classes/database[WRONG].php\n";
?>
