<?php
// book.php
header('Content-Type: application/json');
require_once 'Booking.php';  // Include the OOP class

// Create and process the booking using POST data
$booking = new Booking($_POST);
$booking->processBooking();

