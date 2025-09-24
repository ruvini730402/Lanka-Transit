<?php
require_once __DIR__ . '/../../classes/Database.php';
include('announcement.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $connection = $database->getConnection();
    $announcement = new Announcement($connection);
    if ($announcement->delete($_GET['id'])) {
        header("Location: ../announcement_display.php?msg=Announcement deleted");
    } else {
        header("Location: ../announcement_display.php?msg=Failed to delete");
    }
}
?>

