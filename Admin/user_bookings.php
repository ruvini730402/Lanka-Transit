<?php
require_once __DIR__ . '/../classes/Database.php';
include('php/Booking.php');

try {
    $database = new Database();
    $connection = $database->getConnection();
    $bookingObj = new Booking($connection);
    $bookings = $bookingObj->getAll();
    $buses = $bookingObj->getAllBuses();
    $users = $bookingObj->getAllUsers();
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>

    <!-- Page Heading -->
    <h1 class="text-center fw-bold mb-4">Booking Management</h1>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['msg'])): ?>
        <?php
        $msg = $_GET['msg'];
        $isDelete = stripos($msg, 'delete') !== false;
        $alertClass = $isDelete ? 'alert-danger' : 'alert-success';
        ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Add Button -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addModal">Add Booking</button>
    </div>

    <!-- Bookings Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Passenger</th>
                    <th>Bus</th>
                    <th>Seat</th>
                    <th>Phone</th>
                    <th>Fare</th>
                    <th>Travel Date</th>
                    <th>Route</th>
                    <th>Status</th>
                    <th>Booked At</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['ID']) ?></td>
                        <td>
                            <?php if ($booking['PassengerName']): ?>
                                <strong><?= htmlspecialchars($booking['PassengerName']) ?></strong>
                                <br><small class="text-muted">User ID: <?= htmlspecialchars($booking['UserId']) ?></small>
                            <?php else: ?>
                                <em class="text-muted">Walk-in Customer</em>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($booking['BusNumber']) ?></td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($booking['SeatNumber']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($booking['PhoneNumber']) ?></td>
                        <td>LKR <?= number_format($booking['Fare'], 2) ?></td>
                        <td><?= htmlspecialchars($booking['TravelDate']) ?></td>
                        <td>
                            <small><?= htmlspecialchars($booking['Origin']) ?> → <?= htmlspecialchars($booking['Destination']) ?></small>
                        </td>
                        <td>
                            <span class="badge <?= 
                                $booking['Status'] == 'confirmed' ? 'bg-success' : 
                                ($booking['Status'] == 'cancelled' ? 'bg-danger' : 'bg-secondary') 
                            ?>">
                                <?= htmlspecialchars($booking['Status']) ?>
                            </span>
                        </td>
                        <td>
                            <small><?= date('Y-m-d H:i', strtotime($booking['BookingTime'])) ?></small>
                        </td>
                        <td>
                            <a href="php/update_booking.php?id=<?= $booking['ID'] ?>" class="btn btn-success btn-sm">Update</a>
                        </td>
                        <td>
                            <!-- Delete Button triggers Modal -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $booking['ID'] ?>">
                                Delete
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal<?= $booking['ID'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $booking['ID'] ?>" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="deleteLabel<?= $booking['ID'] ?>">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure you want to delete booking <strong>#<?= htmlspecialchars($booking['ID']) ?></strong>?
                                    <br><small class="text-muted">Seat: <?= htmlspecialchars($booking['SeatNumber']) ?> | Bus: <?= htmlspecialchars($booking['BusNumber']) ?></small>
                                    <br><small class="text-danger">This action cannot be undone and will also delete related payment records.</small>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <a href="php/delete_booking.php?id=<?= $booking['ID'] ?>" class="btn btn-danger">Delete</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center text-muted">No bookings found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Booking Modal -->
<form action="php/insert_booking.php" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Passenger (Optional)</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Walk-in Customer</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= htmlspecialchars($user['ID']) ?>">
                                            <?= htmlspecialchars($user['Name']) ?> (<?= htmlspecialchars($user['Email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bus</label>
                                <select name="bus_id" class="form-select" required>
                                    <option value="">Select a bus...</option>
                                    <?php foreach ($buses as $bus): ?>
                                        <option value="<?= htmlspecialchars($bus['ID']) ?>">
                                            <?= htmlspecialchars($bus['BusNumber']) ?> - <?= htmlspecialchars($bus['Origin']) ?> → <?= htmlspecialchars($bus['Destination']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Seat Number</label>
                                <input type="text" name="seat_number" class="form-control" required maxlength="6" placeholder="e.g., A1, B2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control" required pattern="[0-9]{10}" 
                                       title="Please enter a 10-digit phone number" placeholder="0771234567">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fare (LKR)</label>
                                <input type="number" name="fare" class="form-control" required min="0" step="0.01" placeholder="450.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Travel Date</label>
                                <input type="date" name="travel_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Origin</label>
                                <input type="text" name="origin" class="form-control" required maxlength="50" placeholder="Colombo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Destination</label>
                                <input type="text" name="destination" class="form-control" required maxlength="50" placeholder="Kandy">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Not specified</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_booking" class="btn btn-maroon w-100">Add Booking</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Set today as minimum travel date
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="travel_date"]').setAttribute('min', today);
    document.querySelector('input[name="travel_date"]').value = today;
});

// Form validation
document.querySelector('#addModal form').addEventListener('submit', function(e) {
    const fare = this.querySelector('input[name="fare"]').value;
    const phone = this.querySelector('input[name="phone_number"]').value.trim();
    const seatNumber = this.querySelector('input[name="seat_number"]').value.trim();
    
    if (fare <= 0) {
        e.preventDefault();
        alert('Fare must be greater than 0.');
        return false;
    }
    
    if (!/^[0-9]{10}$/.test(phone)) {
        e.preventDefault();
        alert('Please enter a valid 10-digit phone number.');
        return false;
    }
    
    if (seatNumber.length < 1) {
        e.preventDefault();
        alert('Please enter a seat number.');
        return false;
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
