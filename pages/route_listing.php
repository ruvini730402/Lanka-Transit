<?php
session_start();
require_once('../classes/Database.php');
require_once('../classes/Route.php');

$connection = Database::getConnection();

try {
    $routeObj = new Route($connection);
    $routes = $routeObj->getAllRoutes();
} catch (PDOException $e) {
    $_SESSION['error_msg'] = "Error fetching routes: " . $e->getMessage();
}

// Get any form data that might have been saved in session
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Route Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <?php include('../includes/toast_styles.php'); ?>
</head>
<body>
<div class="container mt-4">
    <a href="admin.php" class="btn btn-maroon-outline back-btn">&larr; Back</a>
    <h1 class="text-center mb-4">Route Management</h1>

    <?php include('../includes/toast_messages.php'); ?>

    <!-- Add Button -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addModal">Add Route</button>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>            
            <th>Origin</th>
            <th>Destination</th>
            <th>Stops</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($routes)): ?>
            <?php foreach ($routes as $route): ?>
                <tr>
                    <td><?= htmlspecialchars($route['ID']) ?></td>
                    <td><?= htmlspecialchars($route['Origin'] ?? '') ?></td>
                    <td><?= htmlspecialchars($route['Destination'] ?? '') ?></td>
                    <td><?= htmlspecialchars($route['Stops'] ?? '') ?></td>
                    <td>
                        <a href="../php/update_route.php?id=<?= $route['ID'] ?>" class="btn btn-success btn-sm">Update</a>
                        <!-- Delete Button triggers Modal -->
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $route['ID'] ?>">
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
                                        Are you sure you want to delete this route from <?= htmlspecialchars($route['Origin']) ?> to <?= htmlspecialchars($route['Destination']) ?>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <a href="../php/delete_route.php?id=<?= $route['ID'] ?>" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No routes found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Route Modal -->
<form action="../php/insert_route.php" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Origin</label>
                        <input type="text" name="origin" class="form-control" required minlength="2" maxlength="100"
                               value="<?= htmlspecialchars($form_data['origin'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Destination</label>
                        <input type="text" name="destination" class="form-control" required minlength="2" maxlength="100"
                               value="<?= htmlspecialchars($form_data['destination'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stops (comma separated)</label>
                        <textarea name="stops" class="form-control" rows="3" maxlength="500"
                                  placeholder="Stop 1, Stop 2, Stop 3..."><?= htmlspecialchars($form_data['stops'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_route" class="btn btn-maroon w-100">Add Route</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
