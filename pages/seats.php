<?php
header('Content-Type: application/json');

// Include the Database class
require_once __DIR__ . '/../classes/Database.php'; // Adjust path if needed

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($db === null) {
        throw new PDOException("Database connection failed.");
    }

    $stmt = $db->query("SELECT SeatNumber, gender FROM Booking");
    $booked = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($booked);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>


