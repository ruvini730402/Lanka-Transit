<?php
session_start();
require_once('../classes/Database.php');
include('../classes/Bus.php');

// Create database connection
$connection = Database::getConnection();

if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = "Invalid request: Bus ID not provided";
    header("Location: ../pages/buslisting.php");
    exit;
}

try {
    $busObj = new Bus($connection);
    
    if ($busObj->deleteBus($_GET['id'])) {
        $_SESSION['success_msg'] = "Bus deleted successfully!";
    } else {
        throw new Exception("Failed to delete bus");
    }
} catch (Exception $e) {
    $_SESSION['error_msg'] = $e->getMessage();
}

header("Location: ../pages/buslisting.php");
exit;
?>
