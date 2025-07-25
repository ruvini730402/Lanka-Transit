<?php
session_start();

$showModal = false;
$errorMsg = "";
$successMsg = "";
$formData = ['bus_no'=>'', 'route'=>'', 'driver_contact'=>'', 'status'=>'', 'seat_count'=>''];

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

require_once '../config/database.php';
require_once '../classes/Bus.php';

$database = new Database();
$connection = $database->getConnection();

if ($connection) {
    $busObj = new Bus($connection);
    $buses = $busObj->getAll();
    if (!$buses) {
        $buses = [];
    }
} else {
    $buses = [];
    $errorMsg = "Database connection failed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Buses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/admin-style.css" />
</head>
<body>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-maroon-outline back-btn">&larr; Back</a>


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
            <th>Bus No</th>
            <th>Route</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Seats</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($buses)): ?>
            <?php foreach ($buses as $bus): ?>
                <tr>
                    <td><?= htmlspecialchars($bus['bus_no']) ?></td>
                    <td><?= htmlspecialchars($bus['route']) ?></td>
                    <td><?= htmlspecialchars($bus['driver_contact']) ?></td>
                    <td><?= htmlspecialchars($bus['status']) ?></td>
                    <td><?= htmlspecialchars($bus['seat_count']) ?></td>
                    <td>
                        <a href="php/update_buslist.php?bus_no=<?= urlencode($bus['bus_no']) ?>" class="btn btn-success btn-sm">Update</a>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn" 
                                data-busno="<?= htmlspecialchars($bus['bus_no']) ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No buses found.</td></tr>
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
                    </div>
                    <div class="mb-3">
                        <label for="route" class="form-label">Route</label>
                        <input type="text" class="form-control" name="route" placeholder="Ex: Colombo - Kandy" required value="<?= htmlspecialchars($formData['route']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="driver_contact" class="form-label">Driver Contact</label>
                        <input type="tel" class="form-control" name="driver_contact" pattern="[0-9]{10}" placeholder="Ex: 0771234567" required value="<?= htmlspecialchars($formData['driver_contact']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">-- Select Status --</option>
                            <option value="Active" <?= ($formData['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= ($formData['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="seat_count" class="form-label">Seat Count</label>
                        <input type="number" class="form-control" name="seat_count" min="1" placeholder="Ex: 45" required value="<?= htmlspecialchars($formData['seat_count']) ?>" />
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
    const contact = document.querySelector("input[name='driver_contact']").value.trim();
    const seats = document.querySelector("input[name='seat_count']").value.trim();
    const errorDiv = document.querySelector(".modal-body .alert-danger");

    const busNoPattern = /^[A-Z]{2,3}-\d{4}$/;
    const contactPattern = /^\d{10}$/;

    let errorMsg = "";

    if (!busNoPattern.test(busNo)) {
        errorMsg = "❌ Invalid Bus Number. Format should be NB-1234 or ABC-5678.";
    } else if (!contactPattern.test(contact)) {
        errorMsg = "❌ Invalid Driver Contact. Must be exactly 10 digits.";
    } else if (!seats || isNaN(seats) || parseInt(seats) <= 0) {
        errorMsg = "❌ Invalid Seat Count. Must be a number greater than 0.";
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
