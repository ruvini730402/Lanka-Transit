<?php
// Include database configuration
require_once '../classes/Database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cancellationId = trim($_POST['cancellation_id'] ?? '');
    $newStatus = trim($_POST['new_status'] ?? '');

    // Debug logging to see what we're receiving
    error_log("DEBUG: Received POST data: " . print_r($_POST, true));
    error_log("DEBUG: cancellation_id = '$cancellationId', new_status = '$newStatus'");

    // Validate cancellation ID
    if (empty($cancellationId)) {
        error_log("Invalid cancellation ID provided: '$cancellationId'");
        header("Location: manage_cancellations.php?error=invalid_id");
        exit;
    }
    
    // Check if status is empty (user didn't select anything)
    if (empty($newStatus)) {
        error_log("No status selected for cancellation ID: " . $cancellationId);
        header("Location: manage_cancellations.php?error=no_status");
        exit;
    }
    
    // Validate status - match BookingCancellation enum values from schema_4.sql
    $allowedStatuses = ['pending', 'refunded', 'declined'];
    if (!in_array($newStatus, $allowedStatuses)) {
        error_log("Invalid status provided: " . $newStatus);
        header("Location: manage_cancellations.php?error=invalid_status");
        exit;
    }

    // Debug logging
    error_log("DEBUG: Processing cancellation ID: $cancellationId, New Status: '$newStatus'");

    $currentDateTime = date("Y-m-d H:i:s");

    try {
        // Start transaction for data consistency
        $conn->beginTransaction();
        error_log("DEBUG: Transaction started");

        // First, check if the cancellation ID exists
        $checkStmt = $conn->prepare("SELECT ID, Status FROM BookingCancellation WHERE ID = ?");
        $checkStmt->execute([$cancellationId]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingRecord) {
            error_log("ERROR: Cancellation ID $cancellationId not found in database");
            $conn->rollback();
            header("Location: manage_cancellations.php?error=not_found");
            exit;
        }
        
        error_log("DEBUG: Found existing record: " . print_r($existingRecord, true));

        // Update the cancellation status
        if ($newStatus === "refunded") {
            // When refunded, also set ProcessedAt timestamp
            $stmt = $conn->prepare("UPDATE BookingCancellation SET Status = ?, ProcessedAt = ? WHERE ID = ?");
            $result = $stmt->execute([$newStatus, $currentDateTime, $cancellationId]);
            error_log("DEBUG: UPDATE query for refunded status executed. Result: " . ($result ? 'SUCCESS' : 'FAILED'));
        } else {
            // For other statuses, just update the status
            $stmt = $conn->prepare("UPDATE BookingCancellation SET Status = ? WHERE ID = ?");
            $result = $stmt->execute([$newStatus, $cancellationId]);
            error_log("DEBUG: UPDATE query for status '$newStatus' executed. Result: " . ($result ? 'SUCCESS' : 'FAILED'));
        }
        
        if (!$result) {
            error_log("ERROR: Failed to update cancellation status");
            $conn->rollback();
            header("Location: manage_cancellations.php?error=update_failed");
            exit;
        }

        // Check if the update actually affected any rows
        $rowCount = $stmt->rowCount();
        error_log("DEBUG: Rows affected by update: $rowCount");

        // If cancellation is refunded, we should update the booking status as well
        if ($newStatus === 'refunded') {
            // Get the booking ID for this cancellation
            $stmt = $conn->prepare("SELECT BookingID FROM BookingCancellation WHERE ID = ?");
            $stmt->execute([$cancellationId]);
            $bookingData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("DEBUG: Booking data for cancellation: " . print_r($bookingData, true));
            
            if ($bookingData && $bookingData['BookingID']) {
                // Update booking status to cancelled if cancellation is refunded
                $bookingUpdateStmt = $conn->prepare("UPDATE Booking SET Status = 'cancelled' WHERE ID = ?");
                $bookingResult = $bookingUpdateStmt->execute([$bookingData['BookingID']]);
                
                error_log("DEBUG: Booking update result: " . ($bookingResult ? 'SUCCESS' : 'FAILED'));
                error_log("Booking " . $bookingData['BookingID'] . " marked as cancelled due to refunded cancellation " . $cancellationId);
            }
        }

        // Commit the transaction
        $conn->commit();
        error_log("DEBUG: Transaction committed successfully");

        error_log("Cancellation ID $cancellationId status updated to '$newStatus' successfully");
        header("Location: manage_cancellations.php?success=updated");
        exit;

    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        
        $errorMessage = "Database error updating cancellation status: " . $e->getMessage();
        $errorCode = $e->getCode();
        error_log("$errorMessage (Code: $errorCode)");
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Provide more specific error messages based on error codes
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            header("Location: manage_cancellations.php?error=column_error");
        } elseif (strpos($e->getMessage(), 'Table') !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
            header("Location: manage_cancellations.php?error=table_error");
        } else {
            header("Location: manage_cancellations.php?error=db_error");
        }
        exit;
    } catch (Exception $e) {
        // Handle any other exceptions
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        
        error_log("General error updating cancellation status: " . $e->getMessage());
        header("Location: manage_cancellations.php?error=general_error");
        exit;
    }

} else {
    // Invalid request method or missing parameters
    error_log("Invalid request to update_cancellations.php");
    header("Location: manage_cancellations.php?error=invalid_request");
    exit;
}
?>