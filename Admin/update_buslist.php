<?php
include('dbcon.php');
include('../classes/Bus.php');


$busObj = new Bus($connection);

// Step 1: Load the existing bus data when accessed with GET

// Use bus ID for update
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $bus = $busObj->getBus($_GET['id']);
    // Redirect if bus not found
    if (!$bus) {
        header("Location: ../buslisting.php?error_msg=Bus not found");
        exit();
    }
}

// Step 2: Handle the update when the form is submitted

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_bus'])) {
    $id = $_POST['bus_id'];
    $routeId = $_POST['route_id'];
    $adminId = $_POST['admin_id'];
    $busNumber = $_POST['bus_number'];
    $capacity = $_POST['capacity'];
    $lastUpdate = date('Y-m-d H:i:s');

    $updated = $busObj->updateBus($id, $routeId, $adminId, $busNumber, $capacity, $lastUpdate);
    if ($updated) {
        header("Location: buslisting.php?update_msg=Bus updated successfully");
        exit();
    } else {
        echo "<div style='color:red; padding:10px;'>Update failed. Please try again.</div>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Update Bus</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<div class="container mt-4">
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
</body>
</html>
