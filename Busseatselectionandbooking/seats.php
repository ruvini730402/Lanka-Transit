<?php
header('Content-Type: application/json');
try {
    $db = new PDO("mysql:host=localhost;dbname=busbooking", "root", "");
    $stmt = $db->query("SELECT seat, gender FROM bookings");
    $booked = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($booked);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>

