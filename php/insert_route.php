<?php
session_start();
require_once('../classes/Database.php');
require_once('../classes/Route.php');

$connection = Database::getConnection();

if (isset($_POST['add_route'])) {
    $origin = trim($_POST['origin']);
    $destination = trim($_POST['destination']);
    $stops = trim($_POST['stops']);

    // Validate inputs
    if (empty($origin) || empty($destination)) {
        $_SESSION['error_msg'] = "Origin and destination are required";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../pages/route_listing.php");
        exit();
    }

    // Check if route already exists
    try {
        $checkStmt = $connection->prepare("SELECT ID FROM Route WHERE Origin = ? AND Destination = ?");
        $checkStmt->execute([$origin, $destination]);
        
        if ($checkStmt->fetch()) {
            $_SESSION['error_msg'] = "Route from $origin to $destination already exists";
            $_SESSION['form_data'] = $_POST;
            header("Location: ../pages/route_listing.php");
            exit();
        }

        $route = new Route($connection);
        if ($route->addRoute($origin, $destination, $stops)) {
            $_SESSION['success_msg'] = "Route added successfully!";
        } else {
            throw new Exception("Failed to add route");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
    }
    
    header("Location: ../pages/route_listing.php");
    exit();
} else {
    $_SESSION['error_msg'] = "Invalid request";
    header("Location: ../pages/route_listing.php");
    exit();
}
?>