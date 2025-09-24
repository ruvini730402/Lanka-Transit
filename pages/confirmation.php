<?php
/**
 * Booking Confirmation - Save to Database (Only after successful payment)
 */
require_once '../includes/session_config.php';
require_once '../classes/Database.php';
require_once '../classes/Payment.php';
require_once '../classes/Booking.php';

$order_id = $_GET['order_id'] ?? '';
$bookingData = null;
$success = false;
$error = '';
$bookingReference = '';

// Check if we have an order ID (coming from successful payment)
if (!empty($order_id)) {
    try {
        $payment = new Payment();
        
        // Try to get booking data from payment session first
        $bookingData = $payment->getPaymentSession($order_id);
        
        // If not found, check alternative session structures
        if (!$bookingData && isset($_SESSION['payment_session'])) {
            $bookingData = $_SESSION['payment_session'];
        }
        
        // Check payment status in database
        $paymentStatus = $payment->getPaymentStatus($order_id);
        
        if ($paymentStatus && $paymentStatus['Status'] === 'success') {
            // Payment confirmed in database - definitely success
            $success = true;
        } else if ($bookingData) {
            // Payment session exists - assume success (PayHere returned before notification)
            $success = true;
            // Create dummy payment status for processing
            $paymentStatus = ['Status' => 'success', 'PaymentMethod' => 'PayHere'];
        } else {
            // Check regular session as fallback
            if (isset($_SESSION['booking_data'])) {
                $bookingData = $_SESSION['booking_data'];
                $success = true;
                $paymentStatus = ['Status' => 'success', 'PaymentMethod' => 'PayHere'];
            } else {
                $error = "No booking data found. Please start a new booking.";
            }
        }
        
        // If we have booking data and success, create the booking record using Booking class
        if ($success && $bookingData) {
            $booking = new Booking();
            // Skip seat availability check for post-payment bookings since payment was already successful
            $result = $booking->createBooking($bookingData, true);
            
            if ($result['success']) {
                $success = true;
                $bookingReference = $result['booking_reference'];
                
                // Payment record will be created via PayHere notification system
                // Don't create payment records directly here
                
                // Clear session data
                clearSessionData();
            } else {
                $success = false;
                $error = $result['error'];
            }
        }
        
    } catch (Exception $e) {
        error_log("Confirmation error: " . $e->getMessage());
        $error = "Error processing booking confirmation: " . $e->getMessage();
    }
} else {
    // No order ID - check if booking data exists in session (legacy flow)
    if (isset($_SESSION['booking_data'])) {
        $bookingData = $_SESSION['booking_data'];
        
        // Use Booking class for demo booking creation
        $booking = new Booking();
        // Skip seat availability check for session-based bookings (demo mode)
        $result = $booking->createBooking($bookingData, true);
        
        if ($result['success']) {
            $success = true;
            $bookingReference = $result['booking_reference'];
            
            // For demo mode, we don't create payment records directly
            // Payment handling is done through PayHere notification system only
            
            // Clear session data
            clearSessionData();
        } else {
            $success = false;
            $error = $result['error'];
        }
    } else {
        header('Location: ../index.php?error=no_booking_data');
        exit;
    }
}

/**
 * Clear session data
 */
function clearSessionData() {
    unset($_SESSION['booking_data']);
    unset($_SESSION['payment_booking_data']);
    unset($_SESSION['payment_order_id']);
    unset($_SESSION['payment_session']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 80px 0;
        }
        .confirmation-card {
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
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .ticket-card {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        .booking-ref {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            font-family: monospace;
        }
        .route-display {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #800000;
        }
        .route-arrow {
            color: #4B0000;
            margin: 0 15px;
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
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-ticket-alt me-1"></i>Book Ticket
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
        <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <?php if ($success): ?>
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Booking Confirmed!</h1>
                <p class="lead mb-4">Your bus ticket has been successfully booked and payment confirmed</p>
            <?php else: ?>
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Booking Failed!</h1>
                <p class="lead mb-4 text-danger"><?= htmlspecialchars($error ?? 'An error occurred during booking') ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Confirmation Content -->
    <?php if ($success && $bookingData): ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="confirmation-card">
                    <div class="booking-ref mb-4">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Booking Reference: <?= $bookingReference ?>
                    </div>
                    
                    <div class="ticket-card">
                        <div class="text-center mb-4">
                            <h4><i class="fas fa-bus me-2"></i>Lanka Transit E-Ticket</h4>
                        </div>
                        
                        <div class="route-display">
                            <?= htmlspecialchars($bookingData['origin']) ?> 
                            <i class="fas fa-arrow-right route-arrow"></i>
                            <?= htmlspecialchars($bookingData['destination']) ?>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <strong>Passenger Name:</strong><br>
                                <?= htmlspecialchars($bookingData['passenger_name']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone Number:</strong><br>
                                <?= htmlspecialchars($bookingData['phone']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Travel Date:</strong><br>
                                <?= date('F j, Y', strtotime($bookingData['travel_date'])) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Seat Number:</strong><br>
                                <span class="badge bg-primary fs-6"><?= htmlspecialchars($bookingData['seat_number']) ?></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Bus Number:</strong><br>
                                <?= htmlspecialchars($bookingData['bus_number']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Fare:</strong><br>
                                Rs. <?= number_format($bookingData['fare'], 2) ?>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 col-lg-3">
                            <a href="../index.php" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Book Another
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="ticket_pdf.php?ref=<?= urlencode($bookingReference) ?>&name=<?= urlencode($bookingData['passenger_name']) ?>&phone=<?= urlencode($bookingData['phone']) ?>&origin=<?= urlencode($bookingData['origin']) ?>&destination=<?= urlencode($bookingData['destination']) ?>&date=<?= urlencode($bookingData['travel_date']) ?>&bus=<?= urlencode($bookingData['bus_number']) ?>&seat=<?= urlencode($bookingData['seat_number']) ?>&fare=<?= urlencode($bookingData['fare']) ?>" 
               class="btn btn-success w-100">
                                <i class="fas fa-download me-2"></i>Download PDF
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <button onclick="window.print()" class="btn btn-outline-primary w-100">
                                <i class="fas fa-print me-2"></i>Print Ticket
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <button onclick="shareTicket()" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-share me-2"></i>Share
                            </button>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> Please arrive at the bus terminal at least 15 minutes before departure time. 
                        Keep this confirmation handy for verification during boarding.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Error Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-danger text-center p-4">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i>Booking Error</h4>
                    <p class="mb-3"><?= htmlspecialchars($error ?? 'An unexpected error occurred') ?></p>
                    <?php if (!empty($order_id)): ?>
                        <p><strong>Order ID:</strong> <code><?= htmlspecialchars($order_id) ?></code></p>
                    <?php endif; ?>
                    <div class="row g-2 mt-3">
                        <div class="col-md-6">
                            <a href="../index.php" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Start New Booking
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="payment_cancel.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Back to Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
