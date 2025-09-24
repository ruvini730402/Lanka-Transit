<?php
require_once __DIR__ . '/../../classes/Database.php';
include('User.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $connection = $database->getConnection();
    $user = new User($connection);
    
    if ($user->delete($_GET['id'])) {
        header("Location: ../user_display.php?msg=User deleted successfully");
    } else {
        header("Location: ../user_display.php?msg=Failed to delete user");
    }
    exit();
}
?>