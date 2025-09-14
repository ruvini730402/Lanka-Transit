<?php
/**
 * Search Functionality Test
 * Tests the bus search feature
 */

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Bus.php';

echo "<h2>Lanka Transit - Search Functionality Test</h2>";
echo "<hr>";

// Test 1: Database Connection
echo "<h3>Test 1: Database Connection</h3>";
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Database connection successful<br>";
    echo "Database: lanka_transit<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test 2: Check if tables exist
echo "<h3>Test 2: Database Tables</h3>";
$tables = ['User', 'Bus', 'Route', 'Schedule', 'Seat', 'Booking'];
foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table' exists with $count records<br>";
    } catch (PDOException $e) {
        echo "❌ Table '$table' not found or error: " . $e->getMessage() . "<br>";
    }
}

// Test 3: Search Functionality
echo "<h3>Test 3: Search Functionality</h3>";
$bus = new Bus($db);

// Test search: Badulla to Matara
echo "<strong>Search: Badulla to Matara on 2025-07-20</strong><br>";
$result = $bus->searchBuses('Badulla', 'Matara', '2025-07-20');

if (isset($result['error'])) {
    echo "❌ Search failed: " . $result['error'] . "<br>";
} else {
    echo "✅ Search successful! Found " . count($result['data']) . " buses<br>";
    foreach ($result['data'] as $busData) {
        echo "- Bus " . $busData['bus_number'] . " | Fare: Rs. " . $busData['fare'] . " | Available: " . $busData['available_seats'] . " seats<br>";
    }
}

echo "<br>";

// Test search: Matara to Badulla
echo "<strong>Search: Matara to Badulla on 2025-07-20</strong><br>";
$result2 = $bus->searchBuses('Matara', 'Badulla', '2025-07-20');

if (isset($result2['error'])) {
    echo "❌ Search failed: " . $result2['error'] . "<br>";
} else {
    echo "✅ Search successful! Found " . count($result2['data']) . " buses<br>";
    foreach ($result2['data'] as $busData) {
        echo "- Bus " . $busData['bus_number'] . " | Fare: Rs. " . $busData['fare'] . " | Available: " . $busData['available_seats'] . " seats<br>";
    }
}

echo "<br>";

// Test search with fare filter
echo "<strong>Search: Badulla to Matara with max fare Rs. 380</strong><br>";
$result3 = $bus->searchBuses('Badulla', 'Matara', '2025-07-20', 380);

if (isset($result3['error'])) {
    echo "❌ Search with fare filter failed: " . $result3['error'] . "<br>";
} else {
    echo "✅ Search with fare filter successful! Found " . count($result3['data']) . " buses<br>";
    foreach ($result3['data'] as $busData) {
        echo "- Bus " . $busData['bus_number'] . " | Fare: Rs. " . $busData['fare'] . " | Available: " . $busData['available_seats'] . " seats<br>";
    }
}

// Test 4: Input Validation
echo "<h3>Test 4: Input Validation</h3>";

// Test invalid date
$result4 = $bus->searchBuses('Colombo', 'Kandy', '2024-07-18'); // Past date
if (isset($result4['error'])) {
    echo "✅ Past date validation working: " . $result4['error'] . "<br>";
} else {
    echo "❌ Past date validation failed<br>";
}

// Test same origin and destination
$result5 = $bus->searchBuses('Colombo', 'Colombo', '2024-07-19');
if (isset($result5['error'])) {
    echo "✅ Same origin/destination validation working: " . $result5['error'] . "<br>";
} else {
    echo "❌ Same origin/destination validation failed<br>";
}

// Test 5: Seat Availability
echo "<h3>Test 5: Seat Availability</h3>";
$seatResult = $bus->getAvailableSeats(1, '2024-07-19');

if (isset($seatResult['error'])) {
    echo "❌ Seat availability check failed: " . $seatResult['error'] . "<br>";
} else {
    $availableSeats = array_filter($seatResult['data'], function($seat) {
        return $seat['status'] === 'available';
    });
    $bookedSeats = array_filter($seatResult['data'], function($seat) {
        return $seat['status'] === 'booked';
    });
    
    echo "✅ Seat availability check successful!<br>";
    echo "- Total seats: " . count($seatResult['data']) . "<br>";
    echo "- Available seats: " . count($availableSeats) . "<br>";
    echo "- Booked seats: " . count($bookedSeats) . "<br>";
}

echo "<hr>";
echo "<h3>Test Summary</h3>";
echo "All basic functionality tests completed. If you see ✅ marks above, the system is working correctly.<br>";
echo "You can now test the homepage by visiting <a href='index.php'>index.php</a><br>";
echo "<br><strong>Note:</strong> Make sure to run the database schema and sample data SQL files before testing.";
?>