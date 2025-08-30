<?php
session_start();

$showModal = false;
$errorMsg = "";
$successMsg = "";
$formData = [
    'bus_number' => '',
    'route_id'   => '',
    'admin_id'   => '',
    'capacity'   => '',
    'last_update'=> ''
];

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

include('../php/dbcon.php');
include('../classes/Bus.php');

$busObj = new Bus($connection);
$buses = $busObj->getAllBuses();
if (!$buses) {
    $buses = [];
}

// Handle Add Bus form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bus'])) {
    $busNumber = trim($_POST['bus_number']);
    $routeId = intval($_POST['route_id']);
    $adminId = $_SESSION['admin_id'] ?? 1; // Use session or default admin
    $capacity = intval($_POST['capacity']);
    $lastUpdate = date('Y-m-d H:i:s');

    if ($routeId < 1 || $capacity < 1) {
        $_SESSION['bus_error'] = "Route ID and Capacity must be positive numbers.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: buslisting.php");
        exit;
    }

    try {
        if ($busObj->addBus($routeId, $adminId, $busNumber, $capacity, $lastUpdate)) {
            $_SESSION['bus_success'] = "Bus added successfully!";
        } else {
            throw new Exception("Failed to add bus. Please try again.");
        }
    } catch (Exception $e) {
        $_SESSION['bus_error'] = $e->getMessage();
        $_SESSION['bus_form'] = $_POST;
    }
    
    header("Location: buslisting.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Buses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/admin-style.css" />
    <style>
        .toast {
            opacity: 1 !important;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <a href="admin.php" class="btn btn-maroon-outline back-btn">&larr; Back</a>

    <h1 class="text-center mb-4">Bus List</h1>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php if ($errorMsg): ?>
        <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($successMsg): ?>
        <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= htmlspecialchars($successMsg) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Bus</button>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Bus Number</th>
            <th>Origin</th>
            <th>Destination</th>
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
                    <td><?= htmlspecialchars($bus['Origin'] ?? '') ?></td>
                    <td><?= htmlspecialchars($bus['Destination'] ?? '') ?></td>
                    <td><?= htmlspecialchars($bus['Capacity']) ?></td>
                    <td><?= htmlspecialchars($bus['LastUpdate']) ?></td>
                    <td>
                        <a href="update_buslist.php?id=<?= urlencode($bus['ID']) ?>" class="btn btn-success btn-sm">Update</a>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn" 
                                data-id="<?= htmlspecialchars($bus['ID']) ?>"
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
<form id="addBusForm" action="" method="POST">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add New Bus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bus Number</label>
                        <input type="text" class="form-control" name="bus_number" placeholder="Ex: NB-1234" required value="<?= htmlspecialchars($formData['bus_number']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Route ID</label>
                        <input type="number" class="form-control" name="route_id" min="1" required value="<?= htmlspecialchars($formData['route_id']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <select class="form-select" name="capacity" required>
                            <option value="" disabled <?= empty($formData['capacity']) ? 'selected' : '' ?>>Select Capacity</option>
                            <option value="49" <?= $formData['capacity'] == '49' ? 'selected' : '' ?>>49 Seats</option>
                            <option value="54" <?= $formData['capacity'] == '54' ? 'selected' : '' ?>>54 Seats</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="add_bus" class="btn btn-maroon w-100">Add Bus</button>
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
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this bus?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <a id="confirmDelete" href="#" class="btn btn-danger">Delete Bus</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("addBusForm").addEventListener("submit", function(e) {
    const busNo = document.querySelector("input[name='bus_number']").value.trim();
    const busNoPattern = /^[A-Z]{2,3}-\d{4}$/;

    if (!busNoPattern.test(busNo)) {
        e.preventDefault();
        alert("❌ Invalid Bus Number. Format: NB-1234 or ABC-5678");
    }
});

// Delete confirmation
const deleteButtons = document.querySelectorAll('.delete-btn');
const confirmDelete = document.getElementById('confirmDelete');

deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        confirmDelete.href = `php/delete_bus.php?id=${encodeURIComponent(id)}`;
    });
});
</script>


<?php if ($showModal): ?>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
    myModal.show();
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all toasts
    const toastElList = document.querySelectorAll('.toast');
    const toastList = [...toastElList].map(toastEl => {
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000 // Auto hide after 5 seconds
        });
        toast.show();
        return toast;
    });
});
</script>
</body>
</html>
