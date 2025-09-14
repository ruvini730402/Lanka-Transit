<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../classes/BookingCancellation.php';

$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

$error = '';
$success = '';
$bookings = [];

try {
    $cancellationService = new BookingCancellation();
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_cancellation'])) {
        $bookingId = (int)$_POST['booking_id'];
        $reason = trim($_POST['cancellation_reason']);
        
        if (empty($bookingId)) {
            $error = 'Please select a booking to cancel';
        } elseif (empty($reason)) {
            $error = 'Please provide a cancellation reason';
        } else {
            $result = $cancellationService->submitCancellation($bookingId, $userId, $reason);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['error'];
            }
        }
    }
    
    // Get user's bookings
    $bookings = $cancellationService->getUserBookings($userId);
    
} catch (Exception $e) {
    $error = "An error occurred. Please try again.";
    error_log("Error in cancel_booking.php: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user-dashboard.css">
    <link rel="stylesheet" href="../assets/css/user-cancel-booking.css">

</head>
<body>
    <!-- Desktop Back Button -->
    <a href="../pages/dashboard.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Mobile Top Navigation -->
    <div class="top-nav">
        <div class="logo-section">
            <a href="../index.php">LankaTransit</a>
        </div>
        <div class="user-dropdown">
            <div class="user-section" onclick="toggleDropdown()">
                <img src="../assets/images/uploads/rosalette.jpg" alt="User">
                <span><?= htmlspecialchars($username) ?></span>
                <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-content">
                <a href="../auth/Logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Tabs -->
    <div class="nav-tabs">
        <a href="dashboard.php">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="incidents.php">
            <i class="fas fa-exclamation-triangle"></i> Report Incident
        </a>
        <a href="cancel_booking.php" class="active">
            <i class="fas fa-times-circle"></i> Cancel Booking
        </a>
    </div>

    <!-- Sidebar (Desktop) -->
    <div class="sidebar">
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/images/uploads/dd.png" alt="LankaTransit Logo">
            </a>
        </div>
        <hr style="height: 1px; background-color: #ccc; border: none; width: 100%; margin: 1px auto 15px auto;">

        <div class="user-profile">
            <div class="user-icon">
                <img src="../assets/images/uploads/rosalette.jpg" alt="User Icon">
            </div>
        </div>
        
        <nav class="navigation">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
                <li class="active"><a href="cancel_booking.php"><i class="fas fa-times-circle"></i> Cancel Booking</a></li>
            </ul>
        </nav>
        
        <div class="logout">
            <a href="../auth/Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="cancellation-container">
            <div class="form-card">
                <h2 class="form-title">Booking Cancellation Form</h2>
                <p class="form-subtitle">We understand plans can change. Submit your cancellation request and we'll process it promptly.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="cancel_booking.php">
                    <div class="form-group">
                        <label for="booking_id" class="form-label">Select Your Booking</label>
                        <select class="form-select" id="booking_id" name="booking_id" required>
                            <option value="">-- Select a Booking to Cancel --</option>
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <option value="<?= $booking['booking_id'] ?>">
                                        <?= htmlspecialchars($booking['Origin'] ?? 'Unknown') ?> to <?= htmlspecialchars($booking['Destination'] ?? 'Unknown') ?> 
                                        - Seat: <?= $booking['SeatNumber'] ?> 
                                        - Rs. <?= number_format($booking['Fare'], 2) ?>
                                        - Booked: <?= date('M j, Y', strtotime($booking['BookingTime'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="demo1">Colombo to Kandy - Seat: 12 - Rs. 1,500.00 - Jan 15, 2025</option>
                                <option value="demo2">Galle to Colombo - Seat: 8 - Rs. 800.00 - Jan 20, 2025</option>
                            <?php endif; ?>
                        </select>
                        <?php if (empty($bookings)): ?>
                            <small class="text-muted">Demo bookings shown - your actual bookings will appear when available.</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" 
                                  rows="5" placeholder="Please provide your reason for cancellation..." 
                                  required><?= isset($_POST['cancellation_reason']) ? htmlspecialchars($_POST['cancellation_reason']) : '' ?></textarea>
                    </div>

                    <div class="btn-container">
                        <button type="submit" name="submit_cancellation" class="btn-submit">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDropdown() {
            document.querySelector('.user-dropdown').classList.toggle('show');
        }

        window.onclick = function(event) {
            if (!event.target.matches('.user-section') && !event.target.closest('.user-section')) {
                var dropdown = document.querySelector('.user-dropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }
    </script>
</body>
</html>
