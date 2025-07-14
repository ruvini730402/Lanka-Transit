<?php
$connection = new mysqli("localhost", "root", "", "lankatrasit");
if ($connection->connect_error) {
    die("Connection failed");
}

// Validate form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_single'])) {
    $incidentIdToUpdate = intval($_POST['update_single']);
    $incidentIds = $_POST['incident_ids'] ?? [];
    $newStatuses = $_POST['new_statuses'] ?? [];

    // Find index of this incident ID
    $index = array_search($incidentIdToUpdate, $incidentIds);
    if ($index !== false && isset($newStatuses[$index])) {
        $newStatus = $connection->real_escape_string($newStatuses[$index]);

        // Update status in DB
        $updateQuery = "UPDATE incidents SET status = ? WHERE id = ?";
        $stmt = $connection->prepare($updateQuery);
        $stmt->bind_param("si", $newStatus, $incidentIdToUpdate);

        if ($stmt->execute()) {
            // Success - redirect silently
            header("Location: manage_incident_status.php");
            exit();
        } else {
            echo "Error updating status.";
        }

        $stmt->close();
    } else {
        echo "Invalid incident ID or status.";
    }
} else {
    echo "Invalid request.";
}

$connection->close();
?>
