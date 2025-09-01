<?php
session_start();
require_once('../classes/Database.php');
include('../classes/announcement.php');

$connection = Database::getConnection();

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Check if announcement exists before trying to delete
        $announcement = new Announcement($connection);
        $exists = $announcement->getById($id);
        
        if (!$exists) {
            $_SESSION['error_msg'] = "Announcement not found";
            header("Location: ../pages/announcement_display.php");
            exit();
        }

        if ($announcement->delete($id)) {
            $_SESSION['success_msg'] = "Announcement deleted successfully!";
        } else {
            throw new Exception("Failed to delete announcement");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
    }
    
    header("Location: ../pages/announcement_display.php");
    exit();
} else {
    $_SESSION['error_msg'] = "Invalid request";
    header("Location: ../pages/announcement_display.php");
    exit();
}
?>

