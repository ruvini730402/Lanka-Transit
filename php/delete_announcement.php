<?php
include('../dbcon.php');
include('announcement.php');

if (isset($_GET['id'])) {
    $announcement = new Announcement($connection);
    if ($announcement->delete($_GET['id'])) {
        header("Location: ../announcement_display.php?msg=Announcement deleted");
    } else {
        header("Location: ../announcement_display.php?msg=Failed to delete");
    }
}
?>

