<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Booking.php');

if (isset($_POST['add_booking'])) {
    $userId = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $busId = $_POST['bus_id'];
    $seatNumber = $_POST['seat_number'];
    $phoneNumber = $_POST['phone_number'];
    $fare = $_POST['fare'];
    $travelDate = $_POST['travel_date'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $gender = $_POST['gender'];

    $database = new Database();
    $connection = $database->getConnection();
    $booking = new Booking($connection);
    
    $bookingId = $booking->insert($userId, $busId, $seatNumber, $phoneNumber, $fare, $travelDate, $origin, $destination, $gender);
    
    if ($bookingId) {
        header("Location: ../user_bookings.php?msg=Booking added successfully");
    } else {
        header("Location: ../user_bookings.php?msg=Failed to add booking");
    }
    exit();
}
?>