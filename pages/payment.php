<?php
/**
 * Payment Page - PayHere Integration
 */
require_once '../includes/session_config.php';
require_once '../classes/Database.php';
require_once '../classes/Payment.php';

// Enhanced session debugging for production
$debug_info = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_data_exists' => isset($_SESSION['booking_data']),
    'session_keys' => array_keys($_SESSION ?? []),
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'timestamp' => date('Y-m-d H:i:s')
];

// Log session information for debugging
error_log("Payment.php session debug: " . json_encode($debug_info));

// Check if booking data exists in session with enhanced error handling
if (!isset($_SESSION['booking_data'])) {
    // Log the missing session data error
    error_log("Payment.php: No booking data in session. Session contents: " . json_encode($_SESSION ?? []));
    
    // Try to recover from URL parameters if available
    $recovery_params = [
        'bus_id', 'date', 'origin', 'destination', 'fare', 'bus_number'
    ];
    
    $has_recovery_data = true;
    foreach ($recovery_params as $param) {
        if (!isset($_GET[$param]) || empty($_GET[$param])) {
            $has_recovery_data = false;
            break;
        }
    }
    
    if ($has_recovery_data) {
        // Log recovery attempt
        error_log("Payment.php: Attempting to recover from URL parameters");
        
        // Redirect back to seat booking with preserved parameters
        $redirect_url = 'seatbooking.php?' . http_build_query($_GET);
        $_SESSION['error'] = "Your session expired. Please select your seat again.";
        header("Location: $redirect_url");
        exit;
    }
    
    // If no recovery possible, redirect to index with error
    header('Location: ../index.php?error=no_booking_data');
    exit;
}

$bookingData = $_SESSION['booking_data'];

try {
    // Initialize Payment class
    $payment = new Payment();
    
    // Generate payment form data
    $paymentInfo = $payment->generatePaymentForm($bookingData);
    
    // Store payment session data
    $payment->storePaymentSession($paymentInfo['order_id'], $bookingData);
    
} catch (Exception $e) {
    error_log("Payment initialization error: " . $e->getMessage());
    $_SESSION['error'] = "Payment system error. Please try again.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 80px 0;
        }
        .payment-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .btn-payment {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: bold;
        }
        .booking-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .route-display {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0;
            color: #800000;
        }
        .amount-display {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
        .security-info {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
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
                <i class="fas fa-credit-card fa-4x"></i>
            </div>
            <h1 class="display-4 fw-bold mb-3">Secure Payment</h1>
            <p class="lead mb-4">Complete your bus ticket booking</p>
        </div>
    </section>

    <!-- Payment Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-card">
                    <!-- Booking Summary -->
                    <div class="booking-summary">
                        <h5 class="text-center mb-3">
                            <i class="fas fa-ticket-alt me-2"></i>Booking Summary
                        </h5>
                        
                        <div class="route-display">
                            <?php echo htmlspecialchars($bookingData['origin']); ?> 
                            <i class="fas fa-arrow-right mx-3" style="color: #4B0000;"></i>
                            <?php echo htmlspecialchars($bookingData['destination']); ?>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Passenger:</strong><br>
                                <?php echo htmlspecialchars($bookingData['passenger_name']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone:</strong><br>
                                <?php echo htmlspecialchars($bookingData['phone']); ?>
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
                                <strong>Bus Number:</strong><br>
                                <?php echo htmlspecialchars($bookingData['bus_number']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Order ID:</strong><br>
                                <code><?php echo htmlspecialchars($paymentInfo['order_id']); ?></code>
                            </div>
                        </div>
                        
                        <div class="amount-display">
                            Total Amount: Rs. <?php echo number_format($bookingData['fare'], 2); ?>
                        </div>
                    </div>

                    <!-- Security Information -->
                    <div class="security-info">
                        <h6><i class="fas fa-shield-alt me-2"></i>Secure Payment</h6>
                        <small>
                            Your payment is secured by PayHere. You will be redirected to the PayHere payment gateway 
                            where you can safely enter your payment details.
                        </small>
                    </div>

                    <!-- PayHere Payment Form -->
                    <form method="post" action="<?php echo $paymentInfo['action_url']; ?>" id="paymentForm">
                        <?php foreach ($paymentInfo['form_data'] as $key => $value): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endforeach; ?>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-payment btn-lg">
                                <i class="fas fa-lock me-2"></i>
                                Proceed to Payment
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="seatbooking.php?<?php echo http_build_query([
                            'bus_id' => $bookingData['bus_id'],
                            'date' => $bookingData['travel_date'],
                            'origin' => $bookingData['origin'],
                            'destination' => $bookingData['destination'],
                            'fare' => $bookingData['fare'],
                            'bus_number' => $bookingData['bus_number'],
                            'departure' => $bookingData['departure_time'] ?? '',
                            'arrival' => $bookingData['arrival_time'] ?? ''
                        ]); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Seat Selection
                        </a>
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


