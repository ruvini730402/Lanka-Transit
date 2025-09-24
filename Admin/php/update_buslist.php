<?php
require_once __DIR__ . '/../../includes/session_config.php';
require_once __DIR__ . '/../../classes/Database.php';
include('Bus.php');

$database = new Database();
$connection = $database->getConnection();
$busObj = new Bus($connection);

// Fetch routes for dropdown
$routeStmt = $connection->prepare("SELECT ID, Origin, Destination FROM Route ORDER BY ID");
$routeStmt->execute();
$routes = $routeStmt->fetchAll(PDO::FETCH_ASSOC);

// Get current admin ID from session
$currentAdminId = $_SESSION['admin_id'] ?? 1;

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
    <a href="../buslisting.php" class="btn btn-maroon-outline back-btn">&larr; Back to Bus List</a>
    
    <h2 class="mb-4">Update Bus Details</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="">
                <input type="hidden" name="bus_no" value="<?= htmlspecialchars($bus['BusNumber']) ?>">

                <div class="mb-3">
                    <label for="status" class="form-label">Bus Number</label>
                    <input type="text" id="status" name="status" value="<?= htmlspecialchars($bus['BusNumber']) ?>" class="form-control" readonly>
                    <div class="form-text">Bus number cannot be changed</div>
                </div>

                <div class="mb-3">
                    <label for="route" class="form-label">Route</label>
                    <select class="form-select" id="route" name="route" required>
                        <option value="">Select a route...</option>
                        <?php foreach ($routes as $route): ?>
                            <option value="<?= htmlspecialchars($route['ID']) ?>" 
                                    <?= ($bus['RouteId'] == $route['ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($route['Origin']) ?> → <?= htmlspecialchars($route['Destination']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="admin_info" class="form-label">Assigned Admin</label>
                    <input type="text" class="form-control" value="Admin ID: <?= htmlspecialchars($currentAdminId) ?> (Current User)" readonly />
                    <input type="hidden" name="driver_contact" value="<?= htmlspecialchars($currentAdminId) ?>" />
                    <div class="form-text">Admin is automatically set to current user</div>
                </div>

                <div class="mb-3">
                    <label for="seat_count" class="form-label">Bus Capacity</label>
                    <select class="form-select" id="seat_count" name="seat_count" required>
                        <option value="">Select capacity...</option>
                        <option value="49" <?= ($bus['Capacity'] == '49') ? 'selected' : '' ?>>49 seats (Standard Bus)</option>
                        <option value="54" <?= ($bus['Capacity'] == '54') ? 'selected' : '' ?>>54 seats (Premium Bus)</option>
                    </select>
                </div>

                <button type="submit" name="update_bus" class="btn btn-maroon w-100">Update Bus</button>

            </form>
        </div>
    </div>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const route = document.getElementById('route').value;
    const capacity = document.getElementById('seat_count').value;
    
    if (!route) {
        e.preventDefault();
        alert('Please select a route.');
        return false;
    }
    
    if (!capacity) {
        e.preventDefault();
        alert('Please select bus capacity.');
        return false;
    }
});

// Reset form validation on change
document.getElementById('route').addEventListener('change', function() {
    this.classList.remove('is-invalid');
});

document.getElementById('seat_count').addEventListener('change', function() {
    this.classList.remove('is-invalid');
});
</script>

</body>
</html>
