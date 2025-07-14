<?php


// book.php
header('Content-Type: application/json');
$db = new PDO("mysql:host=localhost;dbname=busbooking", "root", "");

$seat = $_POST['seat'];
$phone = $_POST['phone'];
$name = $_POST['name'] ?? '';
$nic = $_POST['nic'] ?? '';
$gender = $_POST['gender'];

// Check if seat is already booked
$stmt = $db->prepare("SELECT * FROM bookings WHERE seat = ?");
$stmt->execute([$seat]);
if ($stmt->rowCount() > 0) {
    echo json_encode(["success" => false, "message" => "Seat already booked."]);
    exit;
}

// Check Lady Seat rule
if ($seat <= 8 && $gender !== 'female') {
    // Check time logic here if needed (mocked now)
    echo json_encode(["success" => false, "message" => "This is a Lady Seat. Only females can book now."]);
    exit;
}

// Save booking
$booking_id = uniqid("BID");
$stmt = $db->prepare("INSERT INTO bookings (booking_id, seat, phone, name, nic, gender) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$booking_id, $seat, $phone, $name, $nic, $gender]);

echo json_encode(["success" => true, "message" => "Seat booked! Your ID: $booking_id"]);