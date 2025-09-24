<?php
require_once __DIR__ . '/../../classes/Database.php';
include('User.php');

if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $database = new Database();
    $connection = $database->getConnection();
    $user = new User($connection);
    
    if ($user->insert($name, $email, $password, $phone, $role)) {
        header("Location: ../user_display.php?msg=User added successfully");
    } else {
        header("Location: ../user_display.php?msg=Failed to add user - Email may already exist");
    }
    exit();
}
?>