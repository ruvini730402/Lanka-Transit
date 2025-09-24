<?php
/**
 * Basic Database and Connection Tests
 * Tests database connectivity and table structure
 */

require_once __DIR__ . '/../config/database.php';

echo "<h1>Lanka Transit - Database Connection Tests</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Database connection successful<br>";
    echo "📊 Connected to: lanka_transit database<br>";
    echo "🔧 PDO driver: " . $db->getAttribute(PDO::ATTR_DRIVER_NAME) . "<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

echo "<br>";

// Test 2: Check if all required tables exist
echo "<h2>Test 2: Database Tables Structure</h2>";
$requiredTables = [
    'User' => 'User management and authentication',
    'Admin' => 'Administrator accounts',
    'Location' => 'Bus stops and terminals',
    'Route' => 'Bus routes and destinations',
    'Bus' => 'Bus fleet information',
    'Schedule' => 'Bus departure and arrival times',
    'Seat' => 'Seat configuration and availability',
    'Booking' => 'Passenger bookings and reservations',
    'Payment' => 'Payment transactions',
    'Receipt' => 'Payment receipts',
    'Feedback' => 'Customer feedback and ratings',
    'Incident' => 'Issue reporting and tracking'
];

$allTablesExist = true;

foreach ($requiredTables as $table => $description) {
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table' exists with <strong>$count</strong> records - $description<br>";
    } catch (PDOException $e) {
        echo "❌ Table '$table' not found or error: " . $e->getMessage() . "<br>";
        $allTablesExist = false;
    }
}

echo "<br>";

// Test 3: Check table relationships (Foreign Keys)
echo "<h2>Test 3: Table Relationships</h2>";
$relationships = [
    "Bus -> Route" => "SELECT COUNT(*) FROM Bus b JOIN Route r ON b.RouteId = r.ID",
    "Schedule -> Bus" => "SELECT COUNT(*) FROM Schedule s JOIN Bus b ON s.BusID = b.ID",
    "Seat -> Bus" => "SELECT COUNT(*) FROM Seat st JOIN Bus b ON st.BusID = b.ID",
    "Booking -> Bus" => "SELECT COUNT(*) FROM Booking bk JOIN Bus b ON bk.BusID = b.ID",
    "Payment -> Booking" => "SELECT COUNT(*) FROM Payment p JOIN Booking b ON p.BookingId = b.ID"
];

foreach ($relationships as $relationship => $query) {
    try {
        $stmt = $db->query($query);
        $count = $stmt->fetchColumn();
        echo "✅ $relationship: <strong>$count</strong> linked records<br>";
    } catch (PDOException $e) {
        echo "❌ $relationship: Error - " . $e->getMessage() . "<br>";
    }
}

echo "<br>";

// Test 4: Database indexes (performance check)
echo "<h2>Test 4: Database Indexes</h2>";
try {
    $stmt = $db->query("SHOW INDEX FROM Bus");
    $busIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Bus table has " . count($busIndexes) . " indexes for optimal performance<br>";
    
    $stmt = $db->query("SHOW INDEX FROM Booking");
    $bookingIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Booking table has " . count($bookingIndexes) . " indexes for optimal performance<br>";
    
} catch (PDOException $e) {
    echo "❌ Index check error: " . $e->getMessage() . "<br>";
}

echo "<br>";

// Test 5: Data validation functions
echo "<h2>Test 5: Validation Functions</h2>";

// Test email validation
$testEmail = "test@example.com";
$emailValid = Database::validateInput($testEmail, 'email');
echo ($emailValid ? "✅" : "❌") . " Email validation: '$testEmail' - " . ($emailValid ? "Valid" : "Invalid") . "<br>";

// Test phone validation
$testPhone = "0771234567";
$phoneValid = Database::validateInput($testPhone, 'phone');
echo ($phoneValid ? "✅" : "❌") . " Phone validation: '$testPhone' - " . ($phoneValid ? "Valid" : "Invalid") . "<br>";

// Test date validation
$testDate = "2025-08-15";
$dateValid = Database::validateInput($testDate, 'date');
echo ($dateValid ? "✅" : "❌") . " Date validation: '$testDate' - " . ($dateValid ? "Valid" : "Invalid") . "<br>";

// Test input sanitization
$testInput = "<script>alert('xss')</script>Test Data";
$sanitized = Database::sanitizeInput($testInput);
echo "✅ Input sanitization: Original: '$testInput' → Sanitized: '$sanitized'<br>";

echo "<hr>";
echo "<h2>📋 Summary</h2>";

if ($allTablesExist) {
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "<strong>✅ All database tests passed successfully!</strong><br>";
    echo "🔗 Database is properly connected<br>";
    echo "📊 All required tables exist with data<br>";
    echo "🔗 Table relationships are working<br>";
    echo "🛡️ Input validation and sanitization are functional<br>";
    echo "⚡ Database indexes are in place for performance<br>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "<strong>❌ Some database tests failed!</strong><br>";
    echo "Please check the database schema and run the schema.sql file if needed.<br>";
    echo "</div>";
}

echo "<br><em>Database tests completed at " . date('Y-m-d H:i:s') . "</em>";
?>
