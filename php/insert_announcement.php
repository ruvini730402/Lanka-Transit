<?php
session_start();
require_once('../classes/Database.php');
include('../classes/announcement.php');

$connection = Database::getConnection();

if (isset($_POST['add_announcement'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Validate inputs
    if (empty($title) || empty($content)) {
        $_SESSION['error_msg'] = "Title and content are required";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../pages/announcement_display.php");
        exit();
    }

    try {
        $announcement = new Announcement($connection);
        if ($announcement->insert($title, $content)) {
            $_SESSION['success_msg'] = "Announcement added successfully!";
        } else {
            throw new Exception("Failed to add announcement");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
    }
    
    header("Location: ../pages/announcement_display.php");
    exit();
}
?>

