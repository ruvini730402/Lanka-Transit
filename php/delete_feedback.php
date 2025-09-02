<?php
session_start();
require_once('../classes/Database.php');

$connection = Database::getConnection();

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Check if feedback exists
        $checkStmt = $connection->prepare("SELECT ID FROM Feedback WHERE ID = ?");
        $checkStmt->execute([$id]);
        
        if (!$checkStmt->fetch()) {
            $_SESSION['error_msg'] = "Feedback not found";
            header("Location: ../pages/feedback_display.php");
            exit();
        }

        // Delete the feedback
        $stmt = $connection->prepare("DELETE FROM Feedback WHERE ID = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['success_msg'] = "Feedback deleted successfully!";
        } else {
            throw new Exception("Failed to delete feedback");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
    }
    
    header("Location: ../pages/feedback_display.php");
    exit();
} else {
    $_SESSION['error_msg'] = "Invalid request";
    header("Location: ../pages/feedback_display.php");
    exit();
}
?>
