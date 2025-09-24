<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Booking.php');

// Initialize variables
$booking_data = null;
$error_message = '';
$buses = [];
$users = [];

try {
    $database = new Database();
    $connection = $database->getConnection();
    $bookingObj = new Booking($connection);
    
    // Get buses and users for dropdowns
    $buses = $bookingObj->getAllBuses();
    $users = $bookingObj->getAllUsers();
    
    // Get booking data for editing
    if (isset($_GET['id'])) {
        $booking_data = $bookingObj->getById($_GET['id']);
        
        if (!$booking_data) {
            header("Location: ../user_bookings.php?msg=Booking not found");
            exit();
        }
    }
} catch (PDOException $e) {
    $error_message = "Error fetching data: " . $e->getMessage();
}

// Handle form submission
if (isset($_POST['update_booking']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $userId = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $busId = $_POST['bus_id'];
    $seatNumber = $_POST['seat_number'];
    $phoneNumber = $_POST['phone_number'];
    $fare = $_POST['fare'];
    $travelDate = $_POST['travel_date'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $status = $_POST['status'];
    $gender = $_POST['gender'];

    try {
        if ($bookingObj->update($id, $userId, $busId, $seatNumber, $phoneNumber, $fare, $travelDate, $origin, $destination, $status, $gender)) {
            header("Location: ../user_bookings.php?msg=Booking updated successfully");
            exit();
        } else {
            $error_message = "Failed to update booking";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating booking: " . $e->getMessage();
    }
}

// If no booking data and no error, redirect back
if (!$booking_data && !$error_message) {
    header("Location: ../user_bookings.php?msg=Invalid booking ID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="../user_bookings.php" class="btn btn-maroon-outline back-btn">&larr; Back to Bookings</a>
    
    <h2 class="mb-4">Update Booking</h2>

    <!-- Error Message -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($booking_data): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($booking_data['ID']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Passenger</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Walk-in Customer</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= htmlspecialchars($user['ID']) ?>" 
                                            <?= ($booking_data['UserId'] == $user['ID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['Name']) ?> (<?= htmlspecialchars($user['Email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bus_id" class="form-label">Bus</label>
                            <select class="form-select" id="bus_id" name="bus_id" required>
                                <option value="">Select a bus...</option>
                                <?php foreach ($buses as $bus): ?>
                                    <option value="<?= htmlspecialchars($bus['ID']) ?>" 
                                            <?= ($booking_data['BusID'] == $bus['ID']) ? 'selected' : '' ?>>
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
                            <label for="seat_number" class="form-label">Seat Number</label>
                            <input type="text" 
                                   id="seat_number" 
                                   name="seat_number" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($booking_data['SeatNumber']) ?>" 
                                   required 
                                   maxlength="6">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($booking_data['PhoneNumber']) ?>" 
                                   required 
                                   pattern="[0-9]{10}"
                                   title="Please enter a 10-digit phone number">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fare" class="form-label">Fare (LKR)</label>
                            <input type="number" 
                                   id="fare" 
                                   name="fare" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($booking_data['Fare']) ?>" 
                                   required 
                                   min="0" 
                                   step="0.01">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="travel_date" class="form-label">Travel Date</label>
                            <input type="date" 
                                   id="travel_date" 
                                   name="travel_date" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($booking_data['TravelDate']) ?>" 
                                   required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="origin" class="form-label">Origin</label>
                            <input type="text" 
                                   id="origin" 
                                   name="origin" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($booking_data['Origin']) ?>" 
                                   required 
                                   maxlength="50">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <input type="text" 
                                   id="destination" 
                                   name="destination" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($booking_data['Destination']) ?>" 
                                   required 
                                   maxlength="50">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="confirmed" <?= ($booking_data['Status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                <option value="cancelled" <?= ($booking_data['Status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                <option value="completed" <?= ($booking_data['Status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Not specified</option>
                                <option value="Male" <?= ($booking_data['Gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($booking_data['Gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" name="update_booking" class="btn btn-maroon w-100">Update Booking</button>

            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fare = document.getElementById('fare').value;
    const phone = document.getElementById('phone_number').value.trim();
    
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
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>