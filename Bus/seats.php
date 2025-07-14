<?php


// seats.php
header('Content-Type: application/json');
$db = new PDO("mysql:host=localhost;dbname=busbooking", "root", "");
$stmt = $db->query("SELECT seat, gender FROM bookings");
$booked = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($booked);



