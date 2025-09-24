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
    $incidentId = trim($_POST['update_single']);
    $statuses = $_POST['new_statuses'] ?? [];
    $incidentIds = $_POST['incident_ids'] ?? [];

    // Validate incident ID
    if (empty($incidentId)) {
        error_log("Invalid incident ID provided");
        header("Location: manage_incidents.php");
        exit;
    }

    $index = array_search($incidentId, $incidentIds);
    $newStatus = trim($statuses[$index] ?? '');
    
    // Check if status is empty (user didn't select anything)
    if (empty($newStatus)) {
        error_log("No status selected");
        header("Location: manage_incidents.php");
        exit;
    }
    
    // Validate status - match database enum values exactly
    $allowedStatuses = ['submitted', 'in progress', 'resolved'];
    if (!in_array($newStatus, $allowedStatuses)) {
        error_log("Invalid status provided: " . $newStatus);
        header("Location: manage_incidents.php");
        exit;
    }

    $currentDate = date("Y-m-d");

    try {
        // Fixed table name to match schema (Incident with capital I)
        if ($newStatus === "resolved") {
            $stmt = $conn->prepare("UPDATE Incident SET Status = ?, ResolvedDate = ? WHERE ID = ?");
            $stmt->execute([$newStatus, $currentDate, $incidentId]);
        } else {
            $stmt = $conn->prepare("UPDATE Incident SET Status = ?, ResolvedDate = NULL WHERE ID = ?");
            $stmt->execute([$newStatus, $incidentId]);
        }
        
        if ($stmt->rowCount() > 0) {
            error_log("Successfully updated incident ID: " . $incidentId . " to status: " . $newStatus);
        } else {
            error_log("No rows updated for incident ID: " . $incidentId);
        }
        
    } catch (PDOException $e) {
        error_log("Update error: " . $e->getMessage());
    }
}

// Close database connection
$database->closeConnection();
header("Location: manage_incidents.php");
exit;