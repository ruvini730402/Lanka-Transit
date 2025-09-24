<?php
require_once __DIR__ . "/../includes/session_config.php";

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
$cancellationHistory = [];

try {
    $cancellationService = new BookingCancellation();
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_cancellation'])) {
        $bookingId = (int)$_POST['booking_id'];
        $reason = trim($_POST['cancellation_reason']);
        
        if (empty($bookingId)) {
            $error = 'Please select a trip to cancel';
        } elseif (empty($reason)) {
            $error = 'Please tell us why you need to cancel this booking';
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
    
    // Get user's cancellation history
    $cancellationHistory = $cancellationService->getUserCancellationHistory($userId);
    
} catch (Exception $e) {
    $error = "Something went wrong. Please try again or contact support if the issue persists.";
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
        <!-- Booking Cancellation Form Container -->
        <div class="cancellation-container">
            <div class="form-card">
                <h3>❌ Cancel a Booking</h3>
                <hr class="section-divider">
                <p class="form-subtitle">Cancel your upcoming trips if plans change. Note: Only future bookings can be cancelled.</p>

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
                        <label for="booking_id" class="form-label">Select Your Upcoming Booking</label>
                        <select class="form-select" id="booking_id" name="booking_id" required>
                            <option value="">-- Select a Future Booking to Cancel --</option>
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <?php 
                                    $isAlreadyCanceled = !empty($booking['cancellation_id']);
                                    $cancellationStatus = $booking['cancellation_status'] ?? '';
                                    $statusText = '';
                                    
                                    if ($isAlreadyCanceled) {
                                        switch (strtolower($cancellationStatus)) {
                                            case 'pending':
                                                $statusText = ' (Cancellation Pending)';
                                                break;
                                            case 'approved':
                                                $statusText = ' (Cancellation Approved)';
                                                break;
                                            case 'processed':
                                                $statusText = ' (Cancellation Processed)';
                                                break;
                                            case 'rejected':
                                                $statusText = ' (Cancellation Rejected - Contact Support)';
                                                break;
                                            default:
                                                $statusText = ' (Cancellation ' . ucfirst($cancellationStatus) . ')';
                                        }
                                    }
                                    ?>
                                    <option value="<?= $booking['booking_id'] ?>" <?= $isAlreadyCanceled ? 'disabled' : '' ?>>
                                        <?= htmlspecialchars($booking['Origin'] ?? 'Unknown') ?> to <?= htmlspecialchars($booking['Destination'] ?? 'Unknown') ?> 
                                        - Seat: <?= $booking['SeatNumber'] ?> 
                                        - Rs. <?= number_format($booking['Fare'], 2) ?>
                                        - Travel: <?= date('M j, Y', strtotime($booking['TravelDate'])) ?>
                                        <?= $statusText ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No upcoming bookings found. Only future bookings can be cancelled.</option>
                            <?php endif; ?>
                        </select>
                        <?php if (empty($bookings)): ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Only future bookings can be cancelled. Past or completed trips cannot be cancelled.
                            </small>
                        <?php else: ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                All your upcoming trips are shown below. Trips with existing cancellation requests are disabled and marked with their status.
                            </small>
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

        <!-- Cancellation History Container -->
        <div class="cancellation-container">
            <div class="form-card">
                <h3>� Cancellation History</h3>
                <hr class="section-divider">
                <p class="form-subtitle">Track the status of your booking cancellation requests. Admins will update the status as they process your requests.</p>

                <?php if (!empty($cancellationHistory)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Request #</th>
                                    <th>Trip Details</th>
                                    <th>Seat & Fare</th>
                                    <th>Reason</th>
                                    <th>Requested Date</th>
                                    <th>Status</th>
                                    <th>Admin Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cancellationHistory as $cancellation): ?>
                                    <tr>
                                        <td>
                                            <strong>CR-<?= $cancellation['cancellation_id'] ?></strong>
                                        </td>
                                        <td>
                                            <div class="trip-info">
                                                <strong>Bus: <?= htmlspecialchars($cancellation['BusNumber'] ?? 'N/A') ?></strong><br>
                                                <span class="text-muted">
                                                    <?= htmlspecialchars($cancellation['Origin'] ?? 'Unknown') ?> → 
                                                    <?= htmlspecialchars($cancellation['Destination'] ?? 'Unknown') ?>
                                                </span><br>
                                                <small class="text-muted">
                                                    Travel: <?= date('M j, Y g:i A', strtotime($cancellation['BookingTime'])) ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>Seat: <?= $cancellation['SeatNumber'] ?></strong><br>
                                            <span class="text-success">Rs. <?= number_format($cancellation['Fare'], 2) ?></span>
                                        </td>
                                        <td>
                                            <div class="reason-text">
                                                <?= htmlspecialchars($cancellation['CancellationReason']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= date('M j, Y', strtotime($cancellation['RequestedAt'])) ?><br>
                                            <small class="text-muted"><?= date('g:i A', strtotime($cancellation['RequestedAt'])) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusIcon = '';
                                            switch (strtolower($cancellation['cancellation_status'])) {
                                                case 'pending':
                                                    $statusClass = 'status-pending';
                                                    $statusIcon = 'fas fa-clock';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'status-approved';
                                                    $statusIcon = 'fas fa-check-circle';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'status-rejected';
                                                    $statusIcon = 'fas fa-times-circle';
                                                    break;
                                                case 'processed':
                                                    $statusClass = 'status-processed';
                                                    $statusIcon = 'fas fa-check-double';
                                                    break;
                                                default:
                                                    $statusClass = 'status-unknown';
                                                    $statusIcon = 'fas fa-question-circle';
                                            }
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <i class="<?= $statusIcon ?>"></i>
                                                <?= ucfirst($cancellation['cancellation_status']) ?>
                                            </span>
                                            <?php if ($cancellation['ProcessedAt']): ?>
                                                <br><small class="text-muted">
                                                    Processed: <?= date('M j, Y', strtotime($cancellation['ProcessedAt'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($cancellation['AdminNotes']): ?>
                                                <div class="admin-notes">
                                                    <?= htmlspecialchars($cancellation['AdminNotes']) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No notes yet</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data-message">
                        <i class="fas fa-inbox"></i>
                        <h4>No Cancellation Requests Yet</h4>
                        <p>You haven't submitted any booking cancellation requests. When you do, they'll appear here with their status updates.</p>
                    </div>
                <?php endif; ?>
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