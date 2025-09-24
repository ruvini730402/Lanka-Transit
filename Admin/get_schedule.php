<?php
/**
 * Get Schedule Details for Editing
 * Returns schedule information in JSON format
 */

require_once '../classes/Schedule.php';

// Check if schedule ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid schedule ID']);
    exit();
}

$scheduleId = (int)$_GET['id'];

try {
    $schedule = new Schedule();
    $scheduleDetails = $schedule->getScheduleById($scheduleId);
    
    if ($scheduleDetails) {
        echo json_encode([
            'success' => true,
            'schedule' => $scheduleDetails
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Schedule not found'
        ]);
    }
} catch (Exception $e) {
    error_log("Error fetching schedule details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching schedule details'
    ]);
}
?>