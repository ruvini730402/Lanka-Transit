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
    <style>
        /* Mobile Navigation */
        @media (max-width: 768px) {
            .sidebar { display: none !important; }
            .top-nav { display: flex !important; }
            .nav-tabs { display: flex !important; }
        }
        
        .top-nav {
            display: none;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #800000, #a00000);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .top-nav .logo-section a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
        }
        
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-section img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid white;
            margin-right: 10px;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            border-radius: 5px;
            z-index: 1001;
            margin-top: 5px;
        }
        
        .dropdown-content a {
            color: #333 !important;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 5px;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .user-dropdown.show .dropdown-content {
            display: block;
        }
        
        .nav-tabs {
            display: none;
            justify-content: center;
            background: #8B0000;
            padding: 0;
            margin: 0;
            border-bottom: 3px solid #A52A2A;
        }
        
        .nav-tabs a {
            flex: 1;
            text-align: center;
            padding: 15px 10px;
            color: white;
            text-decoration: none;
            border-right: 1px solid rgba(255,255,255,0.2);
            transition: background-color 0.3s ease;
            font-weight: 500;
        }
        
        .nav-tabs a:last-child {
            border-right: none;
        }
        
        .nav-tabs a.active {
            background: rgba(255,255,255,0.2);
            border-bottom: 3px solid white;
        }
        
        .nav-tabs a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        /* Main content positioning for better centering */
        .main-content {
            margin-left: 280px; /* Account for sidebar width */
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        
        .cancellation-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px 40px;
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 50px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-card {
            background: white;
            padding: 50px 60px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 15px;
            color: #8B0000;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .form-subtitle {
            text-align: center;
            margin-bottom: 40px;
            color: #666;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #8B0000;
            margin-bottom: 12px;
            font-size: 15px;
            display: block;
        }
        
        .form-select, .form-control {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px 20px;
            font-size: 16px;
            width: 100%;
            transition: border-color 0.3s;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.25);
            outline: none;
        }
        
        .form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 35px;
        }
        
        .btn-submit {
            background-color: #8B0000;
            border-color: #8B0000;
            color: white;
            padding: 15px 50px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-submit:hover {
            background-color: #A52A2A;
            border-color: #A52A2A;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-submit:focus {
            box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.25);
        }
        
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #800000;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .back-btn { display: none; }
            .main-content {
                margin-left: 0; /* Remove sidebar margin on mobile */
            }
            .cancellation-container {
                padding: 15px 15px;
                padding-top: 30px;
                min-height: auto;
            }
            .form-card {
                padding: 30px 25px;
                border-radius: 10px;
                max-width: 100%;
            }
            .form-title {
                font-size: 1.25rem;
                margin-bottom: 12px;
            }
            .form-subtitle {
                font-size: 0.9rem;
                margin-bottom: 30px;
            }
            .form-group {
                margin-bottom: 25px;
            }
            .btn-submit {
                width: 100%;
                padding: 15px 20px;
            }
            .btn-container {
                margin-top: 25px;
            }
        }
    </style>
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
        <a href="feedback.php">
            <i class="fas fa-comment-alt"></i> Feedback
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
                <li><a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
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
