<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Feedback.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $connection = $database->getConnection();
    $feedback = new Feedback($connection);
    
    if ($feedback->delete($_GET['id'])) {
        header("Location: ../feedback.php?msg=Feedback deleted successfully");
    } else {
        header("Location: ../feedback.php?msg=Failed to delete feedback");
    }
    exit();
}
?>