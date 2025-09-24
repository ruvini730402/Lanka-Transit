<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Route.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $connection = $database->getConnection();
    $route = new Route($connection);
    
    $result = $route->delete($_GET['id']);
    
    if ($result['success']) {
        header("Location: ../route_listing.php?msg=" . urlencode($result['message']));
    } else {
        header("Location: ../route_listing.php?msg=" . urlencode($result['message']));
    }
    exit();
}
?>