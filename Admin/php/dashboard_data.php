<?php
header('Content-Type: application/json');

include('../dbcon.php');        // dbcon is one level up
include('Bus.php');             // inside the same folder
include('User.php');
include('Booking.php');

$bus = new Bus($connection);
$user = new User($connection);
$booking = new Booking($connection);

$data = [
    'total_buses' => $bus->getTotalBuses(),
    'total_users' => $user->getTotalUsers(),
    'total_bookings' => $booking->getTotalBookings()
];

echo json_encode($data);
