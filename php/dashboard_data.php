<?php
header('Content-Type: application/json');

require_once('../config/database.php');
include('Bus.php');             // inside the same folder
include('User.php');
include('Booking.php');

// Create database connection
$database = new Database();
$connection = $database->getConnection();

// Check if connection was successful
if (!$connection) {
    die(json_encode(['error' => 'Database connection failed']));
}

$bus = new Bus($connection);
$user = new User($connection);
$booking = new Booking($connection);

$data = [
    'total_buses' => $bus->getTotalBuses(),
    'total_users' => $user->getTotalUsers(),
    'total_bookings' => $booking->getTotalBookings()
];

echo json_encode($data);
