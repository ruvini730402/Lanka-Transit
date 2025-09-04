<?php
/**
 * Payment Cancel Handler - User is redirected here when payment is cancelled
 */
session_start();
require_once '../config/database.php';
require_once '../classes/Payment.php';

$order_id = $_GET['order_id'] ?? $_SESSION['payment_order_id'] ?? '';
$bookingData = null;

if (!empty($order_id)) {
    try {
        $payment = new Payment();
        $bookingData = $payment->getPaymentSession($order_id);
        
        // Clear payment session
        $payment->clearPaymentSession();
    } catch (Exception $e) {
        error_log("Payment cancel error: " . $e->getMessage());
    }
}

// If we have booking data from session, use it
if (!$bookingData && isset($_SESSION['payment_booking_data'])) {
    $bookingData = $_SESSION['payment_booking_data'];
    unset($_SESSION['payment_booking_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 80px 0;
        }
        .cancel-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: #000;
        }
        .booking-summary {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <span class="fw-bold" style="color: #800000;">Lanka Transit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="mb-4">
                <i class="fas fa-times-circle fa-4x text-warning"></i>
            </div>
            <h1 class="display-4 fw-bold mb-3">Payment Cancelled</h1>
            <p class="lead mb-4">Your payment was cancelled and no charges were made</p>
        </div>
    </section>

    <!-- Cancel Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="cancel-card">
                    <div class="text-center">
                        <h4 class="text-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Payment Cancelled
                        </h4>
                        <p class="lead">You have cancelled the payment process. No charges have been made to your account.</p>
                        
                        <?php if (isset($order_id) && !empty($order_id)): ?>
                            <div class="mt-3">
                                <strong>Cancelled Order ID:</strong> <code><?php echo htmlspecialchars($order_id); ?></code>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($bookingData): ?>
                        <!-- Show booking details that were cancelled -->
                        <div class="booking-summary">
                            <h5 class="text-center mb-3">
                                <i class="fas fa-ticket-alt me-2"></i>Cancelled Booking Details
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Route:</strong><br>
                                    <?php echo htmlspecialchars($bookingData['origin']); ?> → <?php echo htmlspecialchars($bookingData['destination']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Passenger:</strong><br>
                                    <?php echo htmlspecialchars($bookingData['passenger_name']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Travel Date:</strong><br>
                                    <?php echo date('F j, Y', strtotime($bookingData['travel_date'])); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Seat Number:</strong><br>
                                    <?php echo htmlspecialchars($bookingData['seat_number']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Amount:</strong><br>
                                    Rs. <?php echo number_format($bookingData['fare'], 2); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Bus Number:</strong><br>
                                    <?php echo htmlspecialchars($bookingData['bus_number']); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Options to retry or modify booking -->
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <a href="payment.php" class="btn btn-warning w-100">
                                    <i class="fas fa-redo me-2"></i>Retry Payment
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="seatbooking.php?<?php echo http_build_query([
                                    'bus_id' => $bookingData['bus_id'],
                                    'date' => $bookingData['travel_date'],
                                    'origin' => $bookingData['origin'],
                                    'destination' => $bookingData['destination'],
                                    'fare' => $bookingData['fare'],
                                    'bus_number' => $bookingData['bus_number'],
                                    'departure' => $bookingData['departure_time'] ?? '',
                                    'arrival' => $bookingData['arrival_time'] ?? ''
                                ]); ?>" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-edit me-2"></i>Change Seat Selection
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- No booking data available -->
                        <div class="text-center mt-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Your booking session has expired. Please start a new booking.
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="../index.php" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-2"></i>Search New Journey
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="../index.php" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-home me-2"></i>Go to Homepage
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Help and Support -->
                    <div class="mt-5 pt-4 border-top">
                        <div class="text-center">
                            <h6>Need Help?</h6>
                            <p class="text-muted small">
                                If you experienced any issues during payment, please contact our support team.
                            </p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-phone me-2 text-primary"></i>
                                        <span>+94 11 234 5678</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <span>support@lankatransit.com</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-white py-4 mt-5" style="background-color: #800000;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p>&copy; 2025 Lanka Transit. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
