<?php
/**
 * Delete Schedule Script for Lanka Transit Admin
 * Handles schedule deletion with business rule enforcement
 */

require_once '../classes/Database.php';
require_once '../classes/Schedule.php';

// Check if schedule ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid schedule ID']);
    exit();
}

$scheduleId = (int)$_GET['id'];

try {
    $database = new Database();
    $connection = $database->getConnection();
    $schedule = new Schedule($connection);
    
    // Get schedule details first for logging
    $scheduleDetails = $schedule->getScheduleById($scheduleId);
    if (!$scheduleDetails) {
        echo json_encode(['success' => false, 'message' => 'Schedule not found']);
        exit();
    }
    
    // Check if schedule has active bookings
    $bookings = $schedule->getScheduleBookings($scheduleId);
    $activeBookings = array_filter($bookings, function($booking) {
        return $booking['Status'] === 'confirmed';
    });
    
    if (!empty($activeBookings)) {
        $message = "Cannot delete this schedule because it has " . count($activeBookings) . " active booking(s). Please cancel the bookings first.";
        echo json_encode(['success' => false, 'message' => $message]);
        exit();
    }
    
    // Check if the schedule is in the past (additional business rule)
    $departureTime = strtotime($scheduleDetails['DepartureTime']);
    $currentTime = time();
    
    if ($departureTime <= $currentTime) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete past schedules. This schedule has already departed or is currently active.']);
        exit();
    }
    
    // Attempt to delete the schedule
    if ($schedule->deleteSchedule($scheduleId)) {
        // Log the deletion for audit purposes
        error_log("Schedule deleted - ID: {$scheduleId}, Bus: {$scheduleDetails['BusNumber']}, Route: {$scheduleDetails['Origin']} to {$scheduleDetails['Destination']}");
        
        echo json_encode([
            'success' => true, 
            'message' => "Schedule for bus {$scheduleDetails['BusNumber']} on " . date('Y-m-d', strtotime($scheduleDetails['DepartureTime'])) . " has been deleted successfully."
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete schedule. Please try again.']);
    }
    
} catch (Exception $e) {
    error_log("Schedule deletion error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the schedule.']);
}
?>