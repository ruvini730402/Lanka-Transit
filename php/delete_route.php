<?php
session_start();
require_once('../classes/Database.php');
require_once('../classes/Route.php');

$connection = Database::getConnection();

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Check if route exists
        $route = new Route($connection);
        $exists = $route->getRoute($id);
        
        if (!$exists) {
            $_SESSION['error_msg'] = "Route not found";
            header("Location: ../pages/route_listing.php");
            exit();
        }

        // Check if route is being used by any buses
        $checkBusStmt = $connection->prepare("SELECT COUNT(*) as count FROM Bus WHERE RouteId = ?");
        $checkBusStmt->execute([$id]);
        $busCount = $checkBusStmt->fetch()['count'];
        
        if ($busCount > 0) {
            $_SESSION['error_msg'] = "Cannot delete route. It is being used by $busCount bus(es)";
            header("Location: ../pages/route_listing.php");
            exit();
        }

        if ($route->deleteRoute($id)) {
            $_SESSION['success_msg'] = "Route deleted successfully!";
        } else {
            throw new Exception("Failed to delete route");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
    }
    
    header("Location: ../pages/route_listing.php");
    exit();
} else {
    $_SESSION['error_msg'] = "Invalid request";
    header("Location: ../pages/route_listing.php");
    exit();
}
?>