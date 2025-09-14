<?php
session_start();
require_once('../classes/Database.php');
include('../classes/Bus.php');

// Create database connection
$connection = Database::getConnection();

$busObj = new Bus($connection);

// Step 1: Load the existing bus data when accessed with GET

// Use bus ID for update
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $bus = $busObj->getBus($_GET['id']);
    // Redirect if bus not found
    if (!$bus) {
        $_SESSION['error_msg'] = "Bus not found";
        header("Location: buslisting.php");
        exit();
    }
}

// Step 2: Handle the update when the form is submitted

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_bus'])) {
    $id = $_POST['bus_id'];
    $routeId = $_POST['route_id'];
    $adminId = 1;
    $busNumber = $_POST['bus_number'];
    $capacity = $_POST['capacity'];
    $lastUpdate = date('Y-m-d H:i:s');

    try {
        $updated = $busObj->updateBus($id, $routeId, $adminId, $busNumber, $capacity, $lastUpdate);
        if ($updated) {
            $_SESSION['success_msg'] = "Bus updated successfully!";
            header("Location: buslisting.php");
            exit();
        } else {
            throw new Exception("Failed to update bus.");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Update Bus</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <?php include('../includes/toast_styles.php'); ?>

</head>
<body>
<div class="container mt-4">
    <?php include('../includes/toast_messages.php'); ?>
    
    <h2 class="mb-4">Update Bus Details</h2>

    <form method="POST" action="">

        <input type="hidden" name="bus_id" value="<?= htmlspecialchars($bus['ID']) ?>">

        <div class="mb-3">
            <label for="route_id" class="form-label">Route ID</label>
            <input type="number" id="route_id" name="route_id" value="<?= htmlspecialchars($bus['RouteId']) ?>" class="form-control" required>
        </div>

        

        <div class="mb-3">
            <label for="bus_number" class="form-label">Bus Number</label>
            <input type="text" id="bus_number" name="bus_number" value="<?= htmlspecialchars($bus['BusNumber']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="capacity" class="form-label">Capacity</label>
            <input type="number" id="capacity" name="capacity" value="<?= htmlspecialchars($bus['Capacity']) ?>" class="form-control" required>
        </div>

        <button type="submit" name="update_bus" class="btn btn-maroon w-100">Update Bus</button>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
