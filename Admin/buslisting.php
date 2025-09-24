<?php
require_once __DIR__ . '/../includes/session_config.php';

$showModal = false;
$errorMsg = "";
$successMsg = "";
$formData = ['bus_no'=>'', 'route_id'=>'', 'admin_id'=>'', 'capacity'=>''];

if (isset($_SESSION['bus_error'])) {
    $errorMsg = $_SESSION['bus_error'];
    $formData = $_SESSION['bus_form'] ?? $formData;
    $showModal = true;
    unset($_SESSION['bus_error'], $_SESSION['bus_form']);
}

if (isset($_SESSION['bus_success'])) {
    $successMsg = $_SESSION['bus_success'];
    $showModal = true;
    unset($_SESSION['bus_success']);
}

require_once __DIR__ . '/../classes/Database.php';
include('php/Bus.php');

$database = new Database();
$connection = $database->getConnection();
$busObj = new Bus($connection);
$buses = $busObj->getAll();
if (!$buses) {
    $buses = [];
}

// Fetch routes for dropdown
$routeStmt = $connection->prepare("SELECT ID, Origin, Destination FROM Route ORDER BY ID");
$routeStmt->execute();
$routes = $routeStmt->fetchAll(PDO::FETCH_ASSOC);

// Get current admin ID from session
$currentAdminId = $_SESSION['admin_id'] ?? 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Buses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<div class="container mt-4">
    <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>


    <h1 class="text-center mb-4">Bus List</h1>

    <!-- Success and Error Alerts -->
    <?php if (isset($_GET['insert_msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['insert_msg']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['update_msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['update_msg']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['delete_msg'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['delete_msg']) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Bus</button>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Bus Number</th>
            <th>Route ID</th>
            <th>Admin ID</th>
            <th>Capacity</th>
            <th>Last Update</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($buses)): ?>
            <?php foreach ($buses as $bus): ?>
                <tr>
                    <td><?= htmlspecialchars($bus['ID']) ?></td>
                    <td><?= htmlspecialchars($bus['BusNumber']) ?></td>
                    <td><?= htmlspecialchars($bus['RouteId']) ?></td>
                    <td><?= htmlspecialchars($bus['AdminId']) ?></td>
                    <td><?= htmlspecialchars($bus['Capacity']) ?></td>
                    <td><?= htmlspecialchars($bus['LastUpdate']) ?></td>
                    <td>
                        <a href="php/update_buslist.php?bus_no=<?= urlencode($bus['BusNumber']) ?>" class="btn btn-success btn-sm">Update</a>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn" 
                                data-busno="<?= htmlspecialchars($bus['BusNumber']) ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">No buses found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Bus Modal -->
<form id="addBusForm" action="php/insert_bus.php" method="POST">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Bus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
                    <?php elseif ($successMsg): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="bus_no" class="form-label">Bus Number</label>
                        <input type="text" class="form-control" name="bus_no" placeholder="Ex: NB-1234" required value="<?= htmlspecialchars($formData['bus_no']) ?>" />
                        <div class="form-text">Format: NB-1234 (2-3 letters, dash, 4 digits)</div>
                    </div>
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Route</label>
                        <select class="form-select" name="route" required>
                            <option value="">Select a route...</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?= htmlspecialchars($route['ID']) ?>" 
                                        <?= ($formData['route_id'] == $route['ID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($route['Origin']) ?> → <?= htmlspecialchars($route['Destination']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="admin_info" class="form-label">Assigned Admin</label>
                        <input type="text" class="form-control" value="Admin ID: <?= htmlspecialchars($currentAdminId) ?> (Current User)" readonly />
                        <input type="hidden" name="driver_contact" value="<?= htmlspecialchars($currentAdminId) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Bus Capacity</label>
                        <select class="form-select" name="seat_count" required>
                            <option value="">Select capacity...</option>
                            <option value="49" <?= ($formData['capacity'] == '49') ? 'selected' : '' ?>>49 seats (Standard Bus)</option>
                            <option value="54" <?= ($formData['capacity'] == '54') ? 'selected' : '' ?>>54 seats (Premium Bus)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_buses" class="btn btn-maroon w-100">Add Bus</button>
                </div>

            </div>
        </div>
    </div>
</form>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this bus?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="confirmDelete" href="#" class="btn btn-danger">Delete Bus</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("addBusForm").addEventListener("submit", function(e) {
    const busNo = document.querySelector("input[name='bus_no']").value.trim();
    const routeId = document.querySelector("select[name='route']").value;
    const capacity = document.querySelector("select[name='seat_count']").value;
    const errorDiv = document.querySelector(".modal-body .alert-danger");

    const busNoPattern = /^[A-Z]{2,3}-\d{4}$/;

    let errorMsg = "";

    if (!busNoPattern.test(busNo)) {
        errorMsg = "❌ Invalid Bus Number. Format should be NB-1234 or ABC-5678.";
    } else if (!routeId) {
        errorMsg = "❌ Please select a route.";
    } else if (!capacity) {
        errorMsg = "❌ Please select bus capacity.";
    }

    if (errorMsg) {
        e.preventDefault();
        if (errorDiv) {
            errorDiv.textContent = errorMsg;
            errorDiv.style.display = "block";
        } else {
            const div = document.createElement("div");
            div.className = "alert alert-danger";
            div.textContent = errorMsg;
            this.querySelector(".modal-body").prepend(div);
        }
    }
});

// Delete confirmation
const deleteButtons = document.querySelectorAll('.delete-btn');
const confirmDelete = document.getElementById('confirmDelete');

deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const busNo = this.getAttribute('data-busno');
        confirmDelete.href = `php/delete_bus.php?bus_no=${encodeURIComponent(busNo)}`;
    });
});
</script>

<?php if ($showModal): ?>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
    myModal.show();
</script>
<?php endif; ?>

</body>
</html>
