<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Route.php');

if (isset($_POST['add_route'])) {
    $origin = trim($_POST['origin']);
    $destination = trim($_POST['destination']);
    $stops = !empty($_POST['stops']) ? trim($_POST['stops']) : null;

    $database = new Database();
    $connection = $database->getConnection();
    $route = new Route($connection);
    
    if ($route->insert($origin, $destination, $stops)) {
        header("Location: ../route_listing.php?msg=Route added successfully");
    } else {
        header("Location: ../route_listing.php?msg=Failed to add route - Route may already exist");
    }
    exit();
}
?>