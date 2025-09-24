<?php
// book.php
header('Content-Type: application/json');
require_once '../classes/Bus.php';  // Include the Bus class

// Create and process the booking using POST data
$booking = new Booking($_POST);
$booking->processBooking();


