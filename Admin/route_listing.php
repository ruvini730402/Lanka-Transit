<?php
require_once __DIR__ . '/../classes/Database.php';
include('php/Route.php');

try {
    $database = new Database();
    $connection = $database->getConnection();
    $routeObj = new Route($connection);
    
    // Handle search
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    if ($searchTerm) {
        $routes = $routeObj->searchRoutes($searchTerm);
    } else {
        $routes = $routeObj->getAll();
    }
    
    // Get statistics
    $statistics = $routeObj->getRouteStatistics();
} catch (PDOException $e) {
    die("Error fetching routes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Route Management - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-4">
  <!-- Back Button -->
  <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>

  <!-- Page Title -->
  <h1 class="text-center fw-bold mb-4">Route Management</h1>

  <!-- Success/Error Messages -->
  <?php if (isset($_GET['msg'])): ?>
      <?php
      $msg = $_GET['msg'];
      $isDelete = stripos($msg, 'delete') !== false || stripos($msg, 'cannot') !== false;
      $alertClass = $isDelete ? 'alert-danger' : 'alert-success';
      ?>
      <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  <?php endif; ?>

  <!-- Statistics Cards -->
  <div class="row mb-4">
      <div class="col-md-4">
          <div class="card text-center">
              <div class="card-body">
                  <h5 class="card-title">Total Routes</h5>
                  <h2 class="text-primary"><?= $statistics['TotalRoutes'] ?></h2>
              </div>
          </div>
      </div>
      <div class="col-md-4">
          <div class="card text-center">
              <div class="card-body">
                  <h5 class="card-title">Total Buses</h5>
                  <h2 class="text-success"><?= $statistics['TotalBuses'] ?></h2>
              </div>
          </div>
      </div>
      <div class="col-md-4">
          <div class="card text-center">
              <div class="card-body">
                  <h5 class="card-title">Avg Bus Capacity</h5>
                  <h2 class="text-warning"><?= round($statistics['AvgCapacity']) ?> seats</h2>
              </div>
          </div>
      </div>
  </div>

  <!-- Search and Add Controls -->
  <div class="row mb-3">
      <div class="col-md-8">
          <form method="GET" action="" class="d-flex">
              <input type="text" 
                     name="search" 
                     class="form-control me-2" 
                     placeholder="Search routes by origin, destination, or stops..." 
                     value="<?= htmlspecialchars($searchTerm) ?>">
              <button type="submit" class="btn btn-outline-secondary">Search</button>
              <?php if ($searchTerm): ?>
                  <a href="route_listing.php" class="btn btn-outline-danger ms-2">Clear</a>
              <?php endif; ?>
          </form>
      </div>
      <div class="col-md-4 text-end">
          <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addModal">Add Route</button>
      </div>
  </div>

  <!-- Routes Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Route</th>
          <th>Intermediate Stops</th>
          <th>Buses Assigned</th>
          <th>Update</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($routes)): ?>
          <?php foreach ($routes as $route): ?>
            <tr>
              <td><?= htmlspecialchars($route['ID']) ?></td>
              <td>
                  <strong><?= htmlspecialchars($route['Origin']) ?></strong>
                  <br>
                  <span class="text-muted">↓</span>
                  <br>
                  <strong><?= htmlspecialchars($route['Destination']) ?></strong>
              </td>
              <td>
                  <?php if ($route['Stops']): ?>
                      <small><?= htmlspecialchars($route['Stops']) ?></small>
                  <?php else: ?>
                      <em class="text-muted">Direct route</em>
                  <?php endif; ?>
              </td>
              <td>
                  <span class="badge <?= $route['BusCount'] > 0 ? 'bg-success' : 'bg-secondary' ?>">
                      <?= $route['BusCount'] ?> bus<?= $route['BusCount'] != 1 ? 'es' : '' ?>
                  </span>
              </td>
              <td>
                  <a href="php/update_route.php?id=<?= $route['ID'] ?>" class="btn btn-success btn-sm">Update</a>
              </td>
              <td>
                  <!-- Delete Button triggers Modal -->
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $route['ID'] ?>"
                          <?= $route['BusCount'] > 0 ? 'disabled title="Cannot delete route with assigned buses"' : '' ?>>
                      Delete
                  </button>

                  <!-- Delete Confirmation Modal -->
                  <div class="modal fade" id="deleteModal<?= $route['ID'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $route['ID'] ?>" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="deleteLabel<?= $route['ID'] ?>">Confirm Delete</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                  Are you sure you want to delete this route?
                                  <br><strong>Route:</strong> <?= htmlspecialchars($route['Origin']) ?> → <?= htmlspecialchars($route['Destination']) ?>
                                  <?php if ($route['BusCount'] > 0): ?>
                                      <br><small class="text-danger">Warning: This route has <?= $route['BusCount'] ?> bus(es) assigned to it.</small>
                                  <?php endif; ?>
                                  <br><small class="text-danger">This action cannot be undone.</small>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <a href="php/delete_route.php?id=<?= $route['ID'] ?>" class="btn btn-danger">Delete</a>
                              </div>
                          </div>
                      </div>
                  </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
              <td colspan="6" class="text-center text-muted">
                  <?= $searchTerm ? "No routes found matching '$searchTerm'" : "No routes found." ?>
              </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Route Modal -->
<form action="php/insert_route.php" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Origin</label>
                                <input type="text" name="origin" class="form-control" required maxlength="50" 
                                       placeholder="e.g., Colombo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Destination</label>
                                <input type="text" name="destination" class="form-control" required maxlength="50" 
                                       placeholder="e.g., Kandy">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Intermediate Stops (Optional)</label>
                        <textarea name="stops" class="form-control" rows="3" 
                                  placeholder="Enter stops separated by commas (e.g., Kegalle,Mawanella,Kadugannawa)"></textarea>
                        <div class="form-text">Separate multiple stops with commas. Leave empty for direct routes.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <strong>Note:</strong> Make sure to add both directions of the route separately if needed 
                            (e.g., Colombo → Kandy and Kandy → Colombo).
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_route" class="btn btn-maroon w-100">Add Route</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Form validation
document.querySelector('#addModal form').addEventListener('submit', function(e) {
    const origin = this.querySelector('input[name="origin"]').value.trim();
    const destination = this.querySelector('input[name="destination"]').value.trim();
    
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

// Auto-capitalize first letter of origin and destination
document.querySelectorAll('input[name="origin"], input[name="destination"]').forEach(function(input) {
    input.addEventListener('input', function() {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    });
});

// Format stops input
document.querySelector('textarea[name="stops"]').addEventListener('blur', function() {
    if (this.value.trim()) {
        // Split by comma, trim each stop, capitalize first letter, then join back
        const stops = this.value.split(',')
            .map(stop => stop.trim())
            .filter(stop => stop.length > 0)
            .map(stop => stop.charAt(0).toUpperCase() + stop.slice(1))
            .join(',');
        this.value = stops;
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>