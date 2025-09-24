<?php
/**
 * Test booking functionality
 */
require_once '../config/database.php';

// Simulate POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'passenger_name' => 'Test User',
    'phone' => '0771234567',
    'origin' => 'Badulla',
    'destination' => 'Matara',
    'travel_date' => date('Y-m-d', strtotime('+1 day')),
    'bus_id' => '1',
    'seat_number' => 'A1',
    'fare' => '1500.00',
    'demo_mode' => '1'
];

echo "Starting booking test...\n";

try {
    // Test database connection
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    echo "Database connected successfully\n";
    
    // Test basic query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM User");
    $result = $stmt->fetch();
    echo "Current users in database: " . $result['count'] . "\n";
    
    // Now test the booking logic
    $bookingData = [
        'passenger_name' => $_POST['passenger_name'],
        'phone' => $_POST['phone'],
        'origin' => $_POST['origin'],
        'destination' => $_POST['destination'],
        'travel_date' => $_POST['travel_date'],
        'bus_id' => $_POST['bus_id'],
        'seat_number' => $_POST['seat_number'],
        'fare' => (float)$_POST['fare'],
        'demo_mode' => $_POST['demo_mode']
    ];
    
    // Start transaction
    $pdo->beginTransaction();
    echo "Transaction started\n";
    
    // 1. Create user
    $baseEmail = strtolower(str_replace(' ', '', $bookingData['passenger_name'])) . '@demo.com';
    $email = $baseEmail;
    $counter = 1;
    
    $checkStmt = $pdo->prepare("SELECT ID FROM User WHERE Email = ?");
    $checkStmt->execute([$email]);
    
    while ($checkStmt->fetch()) {
        $email = str_replace('@demo.com', $counter . '@demo.com', $baseEmail);
        $counter++;
        $checkStmt->execute([$email]);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO User (Name, Email, PasswordHash, PhoneNumber, Role) 
        VALUES (?, ?, ?, ?, 'guest user')
    ");
    $stmt->execute([
        $bookingData['passenger_name'],
        $email,
        password_hash('demo123', PASSWORD_DEFAULT),
        $bookingData['phone']
    ]);
    $userId = $pdo->lastInsertId();
    echo "User created with ID: $userId\n";
    
    // 2. Create booking
    $stmt = $pdo->prepare("
        INSERT INTO Booking (UserId, BusID, SeatNumber, PhoneNumber, Fare, Status) 
        VALUES (?, ?, ?, ?, ?, 'confirmed')
    ");
    $stmt->execute([
        $userId,
        $bookingData['bus_id'],
        $bookingData['seat_number'],
        $bookingData['phone'],
        $bookingData['fare']
    ]);
    $bookingId = $pdo->lastInsertId();
    echo "Booking created with ID: $bookingId\n";
    
    // 3. Create payment
    $stmt = $pdo->prepare("
        INSERT INTO Payment (BookingId, PaymentMethod, Status, Amount, TransactionId) 
        VALUES (?, 'Demo Payment', 'success', ?, ?)
    ");
    $transactionId = 'TXN-' . time() . '-' . rand(1000, 9999);
    $stmt->execute([
        $bookingId,
        $bookingData['fare'],
        $transactionId
    ]);
    $paymentId = $pdo->lastInsertId();
    echo "Payment created with ID: $paymentId\n";
    
    // Commit transaction
    $pdo->commit();
    echo "Transaction committed successfully\n";
    
    $bookingReference = 'LT-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT);
    echo "Booking Reference: $bookingReference\n";
    echo "SUCCESS: Booking test completed\n";
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollback();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
