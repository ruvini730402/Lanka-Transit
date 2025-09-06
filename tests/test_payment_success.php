<?php
/**
 * Test Script for Payment Success Scenario
 * This script simulates a successful payment return from PayHere
 */
session_start();

// Clear any existing sessions
session_destroy();
session_start();

// Simulate successful payment data
$test_order_id = 'LT-TEST-' . time();
$test_booking_data = [
    'origin' => 'Colombo',
    'destination' => 'Kandy',
    'passenger_name' => 'John Doe',
    'seat_number' => 'A12',
    'amount' => 1500.00,
    'travel_date' => '2025-09-10',
    'departure_time' => '08:00:00'
];

// Set payment session data (simulating what Payment class would store)
$_SESSION['payment_order_id'] = $test_order_id;
$_SESSION['payment_booking_data'] = $test_booking_data;

// Simulate payment record in database (we'll create a mock entry)
require_once '../classes/Database.php';
require_once '../classes/Payment.php';

try {
    $payment = new Payment();
    $db = new Database();
    $conn = $db->getConnection();
    
    // Insert mock payment record
    $stmt = $conn->prepare("
        INSERT INTO Payment (OrderID, Amount, Currency, Status, PaymentMethod, PaymentDate) 
        VALUES (?, ?, 'LKR', 'success', 'PayHere', NOW())
    ");
    $stmt->bind_param("sd", $test_order_id, $test_booking_data['amount']);
    $stmt->execute();
    
    echo "<h2>Test Payment Success Setup Complete</h2>";
    echo "<p><strong>Test Order ID:</strong> {$test_order_id}</p>";
    echo "<p><strong>Test Amount:</strong> LKR " . number_format($test_booking_data['amount'], 2) . "</p>";
    echo "<p><strong>Session Data:</strong> Set successfully</p>";
    echo "<p><strong>Database Record:</strong> Created successfully</p>";
    
    echo "<div style='margin: 20px 0; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<strong>Test URLs:</strong><br>";
    echo "<a href='../pages/payment_return.php?order_id={$test_order_id}' target='_blank' style='display: inline-block; margin: 5px 10px 5px 0; padding: 8px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 3px;'>Test Payment Success Page</a>";
    echo "</div>";
    
    echo "<h3>Test Scenario Details:</h3>";
    echo "<ul>";
    echo "<li>✅ Payment status: SUCCESS</li>";
    echo "<li>✅ Order ID: {$test_order_id}</li>";
    echo "<li>✅ Amount: LKR " . number_format($test_booking_data['amount'], 2) . "</li>";
    echo "<li>✅ Payment method: PayHere</li>";
    echo "<li>✅ Database record exists</li>";
    echo "<li>✅ Session data available</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Success Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: #28a745; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Payment Success Test Setup</h1>
    <div class="info">
        <p>This test simulates a successful payment scenario. Click the test link above to see how the payment return page handles successful payments.</p>
        <p><strong>Expected behavior:</strong></p>
        <ul>
            <li>Shows "Payment Successful" message</li>
            <li>Displays payment details in a green card</li>
            <li>Auto-redirects to confirmation page after 5 seconds</li>
            <li>Shows payment amount, status, method, and date</li>
        </ul>
    </div>
</body>
</html>
