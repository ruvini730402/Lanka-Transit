<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Feedback.php');

if (isset($_POST['add_feedback'])) {
    $userId = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $busId = !empty($_POST['bus_id']) ? $_POST['bus_id'] : null;
    $bookingId = !empty($_POST['booking_id']) ? $_POST['booking_id'] : null;
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];

    $database = new Database();
    $connection = $database->getConnection();
    $feedback = new Feedback($connection);
    
    if ($feedback->insert($userId, $busId, $bookingId, $comment, $rating)) {
        header("Location: ../feedback.php?msg=Feedback added successfully");
    } else {
        header("Location: ../feedback.php?msg=Failed to add feedback");
    }
    exit();
}
?>