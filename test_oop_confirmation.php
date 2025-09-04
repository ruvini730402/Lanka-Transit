<?php
// Test the refactored confirmation page
session_start();

// Clear any existing session data
unset($_SESSION['payment_session']);
unset($_SESSION['booking_data']);

// Simulate a successful payment session
$_SESSION['payment_session'] = array(
    'order_id' => 'LT-OOP-TEST-' . time(),
    'passenger_name' => 'Alice Johnson',
    'phone' => '0771234569',
    'origin' => 'Kandy',
    'destination' => 'Galle',
    'travel_date' => '2025-01-30',
    'bus_number' => 'NB-5678',
    'bus_id' => 1,
    'seat_number' => 'C03',
    'fare' => 850.00,
    'departure_time' => '14:30:00',
    'gender' => 'female',
    'payment_completed' => true
);

$order_id = $_SESSION['payment_session']['order_id'];

// Redirect to confirmation page with order ID
header("Location: pages/confirmation.php?order_id=" . urlencode($order_id));
exit();
?>
