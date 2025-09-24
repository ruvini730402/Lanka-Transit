<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Route.php');

// Initialize variables
$route_data = null;
$error_message = '';
$buses_on_route = [];

try {
    $database = new Database();
    $connection = $database->getConnection();
    $routeObj = new Route($connection);
    
    // Get route data for editing
    if (isset($_GET['id'])) {
        $route_data = $routeObj->getById($_GET['id']);
        
        if (!$route_data) {
            header("Location: ../route_listing.php?msg=Route not found");
            exit();
        }
        
        // Get buses on this route
        $buses_on_route = $routeObj->getBusesOnRoute($_GET['id']);
    }
} catch (PDOException $e) {
    $error_message = "Error fetching data: " . $e->getMessage();
}

// Handle form submission
if (isset($_POST['update_route']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $origin = trim($_POST['origin']);
    $destination = trim($_POST['destination']);
    $stops = !empty($_POST['stops']) ? trim($_POST['stops']) : null;

    try {
        if ($routeObj->update($id, $origin, $destination, $stops)) {
            header("Location: ../route_listing.php?msg=Route updated successfully");
            exit();
        } else {
            $error_message = "Failed to update route - Route with same origin/destination may already exist";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating route: " . $e->getMessage();
    }
}

// If no route data and no error, redirect back
if (!$route_data && !$error_message) {
    header("Location: ../route_listing.php?msg=Invalid route ID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Route</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="../route_listing.php" class="btn btn-maroon-outline back-btn">&larr; Back to Routes</a>
    
    <h2 class="mb-4">Update Route</h2>

    <!-- Error Message -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($route_data): ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Route Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($route_data['ID']) ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="origin" class="form-label">Origin</label>
                                    <input type="text" 
                                           id="origin" 
                                           name="origin" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($route_data['Origin']) ?>" 
                                           required 
                                           maxlength="50"
                                           placeholder="e.g., Colombo">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" 
                                           id="destination" 
                                           name="destination" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($route_data['Destination']) ?>" 
                                           required 
                                           maxlength="50"
                                           placeholder="e.g., Kandy">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="stops" class="form-label">Intermediate Stops (Optional)</label>
                            <textarea id="stops" 
                                      name="stops" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="Enter stops separated by commas (e.g., Kegalle,Mawanella,Kadugannawa)"><?= htmlspecialchars($route_data['Stops']) ?></textarea>
                            <div class="form-text">Separate multiple stops with commas</div>
                        </div>

                        <button type="submit" name="update_route" class="btn btn-maroon w-100">Update Route</button>

                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Buses on this Route</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($buses_on_route)): ?>
                        <div class="list-group">
                            <?php foreach ($buses_on_route as $bus): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($bus['BusNumber']) ?></h6>
                                        <small><?= htmlspecialchars($bus['Capacity']) ?> seats</small>
                                    </div>
                                    <small class="text-muted">Admin: <?= htmlspecialchars($bus['AdminName'] ?: 'Not assigned') ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <small><strong>Note:</strong> This route cannot be deleted while buses are assigned to it.</small>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No buses assigned to this route yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const origin = document.getElementById('origin').value.trim();
    const destination = document.getElementById('destination').value.trim();
    
    if (origin.length < 2) {
        e.preventDefault();
        alert('Origin must be at least 2 characters long.');
        return false;
    }
    
    if (destination.length < 2) {
        e.preventDefault();
        alert('Destination must be at least 2 characters long.');
        return false;
    }
    
    if (origin.toLowerCase() === destination.toLowerCase()) {
        e.preventDefault();
        alert('Origin and destination cannot be the same.');
        return false;
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>