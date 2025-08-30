<?php
include('../dbcon.php');
include('announcement.php');

if (isset($_POST['add_announcement'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $announcement = new Announcement($connection);
    if ($announcement->insert($title, $content)) {
        header("Location: ../announcement_display.php?msg=Announcement added successfully");
    } else {
        header("Location: ../announcement_display.php?msg=Failed to add announcement");
    }
}
?>

