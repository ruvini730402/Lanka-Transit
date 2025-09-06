<?php
/**
 * Payment Return Handler - User returns here after successful payment
 */
session_start();
require_once '../classes/Database.php';
require_once '../classes/Payment.php';

$success = false;
$error = '';
$order_id = $_GET['order_id'] ?? $_SESSION['payment_order_id'] ?? '';
$bookingData = null;

try {
    if (empty($order_id)) {
        throw new Exception("No order ID found");
    }
    
    // Initialize payment processor
    $payment = new Payment();
    
    // First, check if we have booking data from payment session
    $bookingData = $payment->getPaymentSession($order_id);
    
    // Check payment status in database
    $paymentStatus = $payment->getPaymentStatus($order_id);
    $paymentData = null;
    
    if ($paymentStatus && $paymentStatus['Status'] === 'success') {
        // Payment confirmed in database
        $success = true;
        $paymentData = $paymentStatus;
    } else if ($bookingData) {
        // Payment session exists, assume success from PayHere return
        // This handles the case where PayHere redirects before notification is processed
        $success = true;
        // Get payment data from session or create basic info
        $paymentData = [
            'OrderID' => $order_id,
            'Amount' => $bookingData['amount'] ?? $bookingData['total_amount'] ?? 'N/A',
            'Currency' => 'LKR',
            'Status' => 'success',
            'PaymentMethod' => 'PayHere',
            'PaymentDate' => date('Y-m-d H:i:s')
        ];
    } else {
        // No payment record and no session data
        $error = "Payment verification failed. Please contact support with Order ID: " . $order_id;
    }
    
    // If successful and we have booking data, we can proceed
    if ($success && $bookingData) {
        // Clear payment session after successful processing
        $payment->clearPaymentSession();
    }
    
} catch (Exception $e) {
    error_log("Payment return error: " . $e->getMessage());
    $error = "Error processing payment status: " . $e->getMessage();
}

// Fallback: If we don't have booking data from payment session, check regular session
if (!$bookingData && isset($_SESSION['payment_booking_data'])) {
    $bookingData = $_SESSION['payment_booking_data'];
}

// Additional fallback for amount if not found in payment data
if ($success && $paymentData && ($paymentData['Amount'] === 'N/A' || empty($paymentData['Amount']))) {
    // Try to get amount from booking data or session
    $fallbackAmount = null;
    
    if ($bookingData) {
        $fallbackAmount = $bookingData['amount'] ?? $bookingData['total_amount'] ?? $bookingData['fare'] ?? null;
    }
    
    // Check session for amount
    if (!$fallbackAmount && isset($_SESSION['payment_amount'])) {
        $fallbackAmount = $_SESSION['payment_amount'];
    }
    
    if ($fallbackAmount) {
        $paymentData['Amount'] = $fallbackAmount;
    }
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
        #countdown {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1em;
        }
        .amount-display {
            font-weight: 600;
            color: #198754;
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
                <p class="lead mb-4">Redirecting to booking confirmation in <span id="countdown">3</span> seconds...</p>
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
                                Payment Successful
                            </h4>
                            <p>Your payment has been successfully processed. Redirecting you to your booking confirmation...</p>
                            
                            <?php if ($paymentData): ?>
                                <div class="mt-4">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2 text-start">
                                                <div class="col-4"><strong>Order ID:</strong></div>
                                                <div class="col-8"><code><?php echo htmlspecialchars($paymentData['OrderID'] ?? $order_id); ?></code></div>
                                                
                                                <div class="col-4"><strong>Amount:</strong></div>
                                                <div class="col-8">
                                                    <span class="amount-display">
                                                    <?php 
                                                    $amount = $paymentData['Amount'] ?? 'N/A';
                                                    $currency = $paymentData['Currency'] ?? 'LKR';
                                                    
                                                    // Handle different amount formats
                                                    if ($amount !== 'N/A' && is_numeric($amount)) {
                                                        echo $currency . ' ' . number_format((float)$amount, 2);
                                                    } else if ($amount !== 'N/A') {
                                                        // Try to extract numeric value if it's a string with currency
                                                        $numericAmount = preg_replace('/[^0-9.]/', '', $amount);
                                                        if (is_numeric($numericAmount) && $numericAmount > 0) {
                                                            echo $currency . ' ' . number_format((float)$numericAmount, 2);
                                                        } else {
                                                            echo $currency . ' ' . htmlspecialchars($amount);
                                                        }
                                                    } else {
                                                        echo '<span class="text-muted">Amount not available</span>';
                                                    }
                                                    ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="col-4"><strong>Status:</strong></div>
                                                <div class="col-8">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i><?php echo ucfirst($paymentData['Status'] ?? 'Success'); ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="col-4"><strong>Method:</strong></div>
                                                <div class="col-8"><?php echo htmlspecialchars($paymentData['PaymentMethod'] ?? 'PayHere'); ?></div>
                                                
                                                <?php if (isset($paymentData['PaymentDate'])): ?>
                                                <div class="col-4"><strong>Date:</strong></div>
                                                <div class="col-8"><?php echo date('M d, Y g:i A', strtotime($paymentData['PaymentDate'])); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mt-3">
                                    <strong>Order ID:</strong> <code><?php echo htmlspecialchars($order_id); ?></code>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <script>
                            // Countdown and auto-redirect after 3 seconds
                            let countdown = 3;
                            const countdownElement = document.getElementById('countdown');
                            
                            const timer = setInterval(function() {
                                countdown--;
                                if (countdownElement) {
                                    countdownElement.textContent = countdown;
                                }
                                
                                if (countdown <= 0) {
                                    clearInterval(timer);
                                    window.location.href = 'confirmation.php?order_id=<?php echo urlencode($order_id); ?>';
                                }
                            }, 1000);
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
