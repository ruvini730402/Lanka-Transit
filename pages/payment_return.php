<?php
/**
 * Payment Return Handler - User returns here after successful payment
 */
session_start();
require_once '../config/database.php';
require_once '../classes/Payment.php';

$success = false;
$error = '';
$order_id = $_GET['order_id'] ?? $_SESSION['payment_order_id'] ?? '';

try {
    if (empty($order_id)) {
        throw new Exception("No order ID found");
    }
    
    // Initialize payment processor
    $payment = new Payment();
    
    // Check payment status
    $paymentStatus = $payment->getPaymentStatus($order_id);
    
    if ($paymentStatus && $paymentStatus['Status'] === 'success') {
        $success = true;
        $bookingData = $payment->getPaymentSession($order_id);
        
        if ($bookingData) {
            // Redirect to confirmation with order ID
            header('Location: confirmation.php?order_id=' . urlencode($order_id));
            exit;
        }
        
        // Clear payment session
        $payment->clearPaymentSession();
    } else {
        // Payment not yet processed or failed
        // Redirect to a waiting page or show pending status
        $error = "Payment is being processed. Please wait...";
    }
    
} catch (Exception $e) {
    error_log("Payment return error: " . $e->getMessage());
    $error = "Error processing payment status.";
}

// If we have booking data from session, use it
if (!isset($bookingData) && isset($_SESSION['payment_booking_data'])) {
    $bookingData = $_SESSION['payment_booking_data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Payment Successful' : 'Payment Processing'; ?> - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 80px 0;
        }
        .status-card {
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
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #800000;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <?php if ($success): ?>
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Payment Successful!</h1>
                <p class="lead mb-4">Redirecting to booking confirmation...</p>
            <?php else: ?>
                <div class="mb-4">
                    <i class="fas fa-clock fa-4x text-warning"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Processing Payment</h1>
                <p class="lead mb-4">Please wait while we confirm your payment...</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Status Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="status-card">
                    <?php if ($success): ?>
                        <div class="text-center">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Payment Confirmed
                            </h4>
                            <p>Your payment has been successfully processed. You will be redirected to your booking confirmation shortly.</p>
                            
                            <?php if (isset($order_id)): ?>
                                <div class="mt-3">
                                    <strong>Order ID:</strong> <code><?php echo htmlspecialchars($order_id); ?></code>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <a href="confirmation.php?order_id=<?php echo urlencode($order_id); ?>" class="btn btn-primary">
                                    <i class="fas fa-receipt me-2"></i>View Booking Details
                                </a>
                            </div>
                        </div>
                        
                        <script>
                            // Auto-redirect after 3 seconds
                            setTimeout(function() {
                                window.location.href = 'confirmation.php?order_id=<?php echo urlencode($order_id); ?>';
                            }, 3000);
                        </script>
                    <?php else: ?>
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <h4 class="text-warning mb-3">
                                <i class="fas fa-hourglass-half me-2"></i>
                                Processing Payment
                            </h4>
                            <p><?php echo htmlspecialchars($error ?: 'Your payment is being processed. This may take a few moments.'); ?></p>
                            
                            <?php if (isset($order_id)): ?>
                                <div class="mt-3">
                                    <strong>Order ID:</strong> <code><?php echo htmlspecialchars($order_id); ?></code>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <button onclick="window.location.reload()" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-sync me-2"></i>Refresh Status
                                </button>
                                <a href="../index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>Go Home
                                </a>
                            </div>
                        </div>
                        
                        <script>
                            // Auto-refresh every 5 seconds to check payment status
                            setTimeout(function() {
                                window.location.reload();
                            }, 5000);
                        </script>
                    <?php endif; ?>
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
