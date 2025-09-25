<?php
/**
 * Insert Schedule Script for Lanka Transit Admin
 * Handles new schedule creation with validation and constraint enforcement
 */

require_once '../classes/Database.php';
require_once '../classes/Schedule.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busId = filter_input(INPUT_POST, 'bus_id', FILTER_VALIDATE_INT);
    $departureDate = filter_input(INPUT_POST, 'departure_date', FILTER_SANITIZE_STRING);
    $departureTime = filter_input(INPUT_POST, 'departure_time', FILTER_SANITIZE_STRING);
    $arrivalDate = filter_input(INPUT_POST, 'arrival_date', FILTER_SANITIZE_STRING);
    $arrivalTime = filter_input(INPUT_POST, 'arrival_time', FILTER_SANITIZE_STRING);
    $fare = filter_input(INPUT_POST, 'fare', FILTER_VALIDATE_FLOAT);
    
    // Validation
    $errors = [];
    
    if (!$busId) {
        $errors[] = "Please select a valid bus.";
    }
    
    if (!$departureDate || !$departureTime) {
        $errors[] = "Please provide departure date and time.";
    }
    
    if (!$arrivalDate || !$arrivalTime) {
        $errors[] = "Please provide arrival date and time.";
    }
    
    if (!$fare || $fare <= 0) {
        $errors[] = "Please provide a valid fare amount.";
    }
    
    // Combine date and time
    $departureDateTime = $departureDate . ' ' . $departureTime;
    $arrivalDateTime = $arrivalDate . ' ' . $arrivalTime;
    
    // Validate datetime format and logic
    if (!strtotime($departureDateTime)) {
        $errors[] = "Invalid departure date/time format.";
    }
    
    if (!strtotime($arrivalDateTime)) {
        $errors[] = "Invalid arrival date/time format.";
    }
    
    if (strtotime($departureDateTime) >= strtotime($arrivalDateTime)) {
        $errors[] = "Arrival time must be after departure time.";
    }
    
    // Check if departure is in the future
    if (strtotime($departureDateTime) <= time()) {
        $errors[] = "Departure time must be in the future.";
    }
    
    if (empty($errors)) {
        try {
            $database = new Database();
            $connection = $database->getConnection();
            $schedule = new Schedule($connection);
            
            // Check if bus already has a schedule on this date
            $scheduleDate = date('Y-m-d', strtotime($departureDateTime));
            if ($schedule->hasScheduleOnDate($busId, $scheduleDate)) {
                header('Location: schedule_listing.php?msg=This bus already has a schedule on ' . $scheduleDate . '. Each bus can only have one schedule per day.&type=error');
                exit();
            } else {
                // Insert the schedule
                if ($schedule->insertSchedule($busId, $departureDateTime, $arrivalDateTime, $fare)) {
                    header('Location: schedule_listing.php?msg=Schedule created successfully!');
                    exit();
                } else {
                    header('Location: schedule_listing.php?msg=Failed to create schedule. Please try again.&type=error');
                    exit();
                }
            }
        } catch (Exception $e) {
            error_log("Schedule insertion error: " . $e->getMessage());
            header('Location: schedule_listing.php?msg=An error occurred while creating the schedule.&type=error');
            exit();
        }
    } else {
        header('Location: schedule_listing.php?msg=' . urlencode(implode("<br>", $errors)) . '&type=error');
        exit();
    }
}

// If not POST request, redirect to schedule listing
header('Location: schedule_listing.php');
exit();
?>