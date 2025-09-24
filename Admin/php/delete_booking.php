<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Booking.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $connection = $database->getConnection();
    $booking = new Booking($connection);
    
    if ($booking->delete($_GET['id'])) {
        header("Location: ../user_bookings.php?msg=Booking deleted successfully");
    } else {
        header("Location: ../user_bookings.php?msg=Failed to delete booking");
    }
    exit();
}
?>