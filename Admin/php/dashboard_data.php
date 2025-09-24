<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/Database.php';        // Database class
include('Bus.php');             // inside the same folder
include('User.php');
include('Booking.php');

$database = new Database();
$connection = $database->getConnection();
$bus = new Bus($connection);
$user = new User($connection);
$booking = new Booking($connection);

$data = [
    'total_buses' => $bus->getTotalBuses(),
    'total_users' => $user->getTotalUsers(),
    'total_bookings' => $booking->getTotalBookings()
];

echo json_encode($data);
