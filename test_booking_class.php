<?php
// Test the refactored confirmation page with Booking class
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'classes/Database.php';
require_once 'classes/Booking.php';

echo "Testing refactored Booking class...\n\n";

// Clear session
unset($_SESSION['payment_session']);
unset($_SESSION['booking_data']);

// Test booking creation using the new Booking class
$testData = [
    'passenger_name' => 'Jane Smith',
    'phone' => '0771234568',
    'bus_id' => 1,
    'seat_number' => 'B02',
    'fare' => 750.00,
    'gender' => 'female',
    'travel_date' => '2025-01-25'
];

echo "Test data:\n";
print_r($testData);

try {
    $booking = new Booking();
    
    echo "\nTesting booking validation...\n";
    $validation = $booking->validateBookingData($testData);
    echo "Validation result: " . ($validation['valid'] ? "PASSED" : "FAILED - " . $validation['error']) . "\n";
    
    echo "\nTesting seat availability...\n";
    $available = $booking->checkSeatAvailability($testData['bus_id'], $testData['seat_number'], $testData['travel_date']);
    echo "Seat availability: " . ($available ? "AVAILABLE" : "NOT AVAILABLE") . "\n";
    
    echo "\nCreating booking...\n";
    $result = $booking->createBooking($testData);
    
    if ($result['success']) {
        echo "✅ Booking created successfully!\n";
        echo "Booking ID: " . $result['booking_id'] . "\n";
        echo "Booking Reference: " . $result['booking_reference'] . "\n";
        echo "User ID: " . $result['user_id'] . "\n";
        
        // Test getting booking details
        echo "\nTesting booking retrieval...\n";
        $bookingDetails = $booking->getBookingById($result['booking_id']);
        if ($bookingDetails) {
            echo "Retrieved booking for: " . $bookingDetails['PassengerName'] . "\n";
            echo "Seat: " . $bookingDetails['SeatNumber'] . "\n";
            echo "Fare: Rs. " . $bookingDetails['Fare'] . "\n";
        }
        
        // Test gender record
        echo "\nTesting gender record...\n";
        $gender = $booking->getGenderByBooking($result['booking_id']);
        echo "Gender record: " . ($gender ? $gender : "NOT FOUND") . "\n";
        
        // Test total bookings
        echo "\nTotal bookings in system: " . $booking->getTotalBookings() . "\n";
        
    } else {
        echo "❌ Booking creation failed: " . $result['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
