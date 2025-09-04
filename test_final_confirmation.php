<?php
// Test the refactored confirmation page with proper OOP
session_start();

// Clear any existing session data
unset($_SESSION['payment_session']);
unset($_SESSION['booking_data']);

// Simulate a successful payment session
$_SESSION['payment_session'] = array(
    'order_id' => 'LT-FINAL-TEST-' . time(),
    'passenger_name' => 'Michael Chen',
    'phone' => '0771234571',
    'origin' => 'Galle',
    'destination' => 'Colombo',
    'travel_date' => '2025-02-05',
    'bus_number' => 'SB-8888',
    'bus_id' => 1,
    'seat_number' => 'E06',
    'fare' => 680.00,
    'departure_time' => '16:45:00',
    'gender' => 'male',
    'payment_completed' => true
);

$order_id = $_SESSION['payment_session']['order_id'];

// Redirect to confirmation page with order ID
header("Location: pages/confirmation.php?order_id=" . urlencode($order_id));
exit();
?>
