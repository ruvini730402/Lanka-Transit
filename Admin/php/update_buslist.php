<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Bus.php');

$database = new Database();
$connection = $database->getConnection();
$busObj = new Bus($connection);

// Step 1: Load the existing bus data when accessed with GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['bus_no'])) {
    $bus = $busObj->getOne($_GET['bus_no']);

    // Redirect if bus not found
    if (!$bus) {
        header("Location: ../buslisting.php?error_msg=Bus not found");
        exit();
    }
}

// Step 2: Handle the update when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_bus'])) {
    $updated = $busObj->update(
        $_POST['bus_no'],
        $_POST['route'],
        $_POST['driver_contact'],
        $_POST['status'],
        $_POST['seat_count']
    );

    if ($updated) {
        header("Location: ../buslisting.php?update_msg=Bus updated successfully");
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
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Update Bus Details</h2>

    <form method="POST" action="">
        <input type="hidden" name="bus_no" value="<?= htmlspecialchars($bus['BusNumber']) ?>">

        <div class="mb-3">
            <label for="route" class="form-label">Route ID</label>
            <input type="number" id="route" name="route" value="<?= htmlspecialchars($bus['RouteId']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="driver_contact" class="form-label">Admin ID</label>
            <input type="number" id="driver_contact" name="driver_contact" value="<?= htmlspecialchars($bus['AdminId']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Bus Number</label>
            <input type="text" id="status" name="status" value="<?= htmlspecialchars($bus['BusNumber']) ?>" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label for="seat_count" class="form-label">Capacity</label>
            <input type="number" id="seat_count" name="seat_count" value="<?= htmlspecialchars($bus['Capacity']) ?>" class="form-control" required>
        </div>

        <button type="submit" name="update_bus" class="btn btn-maroon w-100">Update Bus</button>

    </form>
</div>
</body>
</html>
