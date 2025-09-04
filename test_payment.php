<?php
// Test payment page scenario
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Payment Page Scenario...\n";

// Test 1: Check session state
echo "1. Session state:\n";
if (isset($_SESSION['booking_data'])) {
    echo "   ✓ Booking data exists in session\n";
    print_r($_SESSION['booking_data']);
} else {
    echo "   ✗ No booking data in session - this would cause redirect\n";
    
    // Create sample booking data for testing
    $_SESSION['booking_data'] = [
        'origin' => 'Badulla',
        'destination' => 'Matara',
        'passenger_name' => 'Test User',
        'phone' => '0771234567',
        'travel_date' => '2025-09-05',
        'seat_number' => '1',
        'bus_number' => 'NB-1001',
        'bus_id' => 1,
        'fare' => 580.00,
        'departure_time' => '06:00:00',
        'arrival_time' => '12:30:00'
    ];
    echo "   ✓ Created sample booking data\n";
}

// Test 2: Test payment generation
echo "\n2. Testing payment generation:\n";
try {
    require_once 'classes/Database.php';
    require_once 'classes/Payment.php';
    
    $payment = new Payment();
    $bookingData = $_SESSION['booking_data'];
    
    $paymentInfo = $payment->generatePaymentForm($bookingData);
    echo "   ✓ Payment form generated successfully\n";
    echo "   Order ID: " . $paymentInfo['order_id'] . "\n";
    echo "   Action URL: " . $paymentInfo['action_url'] . "\n";
    echo "   Form data keys: " . implode(', ', array_keys($paymentInfo['form_data'])) . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Payment generation error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nPayment test completed.\n";
?>
