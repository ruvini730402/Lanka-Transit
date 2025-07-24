<?php
// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_single'])) {
    $bookingId = $_POST['update_single'];
    $statuses = $_POST['new_statuses'] ?? [];
    $bookingIds = $_POST['booking_ids'] ?? [];

    $index = array_search($bookingId, $bookingIds);
    $newStatus = $statuses[$index] ?? 'Pending';

    $currentTime = date("Y-m-d H:i:s");

    try {
        if ($newStatus === "In Progress" || $newStatus === "Resolved") {
            $stmt = $conn->prepare("UPDATE incident SET Status = ?, ResolvedDate = ? WHERE BookingId = ?");
            $stmt->execute([$newStatus, $currentTime, $bookingId]);
        } else {
            $stmt = $conn->prepare("UPDATE incident SET Status = ?, ResolvedDate = NULL WHERE BookingId = ?");
            $stmt->execute([$newStatus, $bookingId]);
        }
    } catch (PDOException $e) {
        error_log("Update error: " . $e->getMessage());
    }
}

// Close database connection
$database->closeConnection();
header("Location: Manage_incidents_status.php");
exit;
