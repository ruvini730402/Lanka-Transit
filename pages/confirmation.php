<?php
/**
 * Booking Confirmation - Save to Database (Only after successful payment)
 */
session_start();
require_once '../config/database.php';
require_once '../classes/Payment.php';

$order_id = $_GET['order_id'] ?? '';
$bookingData = null;
$success = false;
$error = '';

// Check if we have an order ID (coming from successful payment)
if (!empty($order_id)) {
    try {
        $payment = new Payment();
        $paymentStatus = $payment->getPaymentStatus($order_id);
        
        if ($paymentStatus && $paymentStatus['Status'] === 'success') {
            // Payment was successful, get booking data from session
            $bookingData = $payment->getPaymentSession($order_id);
            
            if (!$bookingData && isset($_SESSION['booking_data'])) {
                $bookingData = $_SESSION['booking_data'];
            }
            
            if ($bookingData) {
                // Create the booking record
                $result = createBookingRecord($bookingData, $paymentStatus, $order_id);
                $success = $result['success'];
                $error = $result['error'] ?? '';
                $bookingReference = $result['booking_reference'] ?? '';
            } else {
                $error = "Booking data not found.";
            }
        } else {
            $error = "Payment not confirmed or failed.";
        }
    } catch (Exception $e) {
        error_log("Confirmation error: " . $e->getMessage());
        $error = "Error processing booking confirmation.";
    }
} else {
    // Legacy: Check if booking data exists in session (old flow)
    if (isset($_SESSION['booking_data'])) {
        $bookingData = $_SESSION['booking_data'];
        // For demo purposes, create booking without payment verification
        $result = createBookingRecord($bookingData, ['Status' => 'success'], 'DEMO-' . time());
        $success = $result['success'];
        $error = $result['error'] ?? '';
        $bookingReference = $result['booking_reference'] ?? '';
    } else {
        header('Location: ../index.php?error=no_booking_data');
        exit;
    }
}

function createBookingRecord($bookingData, $paymentStatus, $order_id) {
    try {
        // Get database connection
        $database = new Database();
        $pdo = $database->getConnection();
        
        if (!$pdo) {
            throw new Exception("Database connection failed");
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        // 1. Create user record (guest user for demo)
        $baseEmail = strtolower(str_replace(' ', '', $bookingData['passenger_name'])) . '@demo.com';
        $email = $baseEmail;
        $counter = 1;
        
        // Check if email exists and create unique one if needed
        $checkStmt = $pdo->prepare("SELECT ID FROM User WHERE Email = ?");
        $checkStmt->execute([$email]);
        
        while ($checkStmt->fetch()) {
            $email = str_replace('@demo.com', $counter . '@demo.com', $baseEmail);
            $counter++;
            $checkStmt->execute([$email]);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO User (Name, Email, PasswordHash, PhoneNumber, Role) 
            VALUES (?, ?, ?, ?, 'guest user')
        ");
        $stmt->execute([
            $bookingData['passenger_name'],
            $email,
            password_hash('demo123', PASSWORD_DEFAULT),
            $bookingData['phone']
        ]);
        $userId = $pdo->lastInsertId();
        
        // 2. Create booking record
        $stmt = $pdo->prepare("
            INSERT INTO Booking (UserId, BusID, SeatNumber, PhoneNumber, Fare, Status) 
            VALUES (?, ?, ?, ?, ?, 'confirmed')
        ");
        $stmt->execute([
            $userId,
            $bookingData['bus_id'],
            $bookingData['seat_number'],
            $bookingData['phone'],
            $bookingData['fare']
        ]);
        $bookingId = $pdo->lastInsertId();
        
        // 3. Create booking_2 record for gender tracking
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Booking_2 (booking_id, gender) 
                VALUES (?, ?)
            ");
            $stmt->execute([$bookingId, $bookingData['gender']]);
        } catch (PDOException $e) {
            // Booking_2 table might not exist, continue without it
            error_log("Booking_2 insert failed: " . $e->getMessage());
        }
        
        // 4. Create or update payment record
        $stmt = $pdo->prepare("
            INSERT INTO Payment (BookingId, PaymentMethod, Status, Amount, TransactionId) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            BookingId = VALUES(BookingId),
            Status = VALUES(Status)
        ");
        $paymentMethod = $paymentStatus['PaymentMethod'] ?? 'PayHere Gateway';
        $stmt->execute([
            $bookingId,
            $paymentMethod,
            'success',
            $bookingData['fare'],
            $order_id
        ]);
        
        // 5. Update seat status (if seat table exists for this bus)
        try {
            $stmt = $pdo->prepare("
                UPDATE Seat SET Status = 'booked' 
                WHERE BusID = ? AND SeatNumber = ?
            ");
            $stmt->execute([$bookingData['bus_id'], $bookingData['seat_number']]);
        } catch (PDOException $e) {
            // Seat table might not exist for this bus, continue without it
            error_log("Seat update failed: " . $e->getMessage());
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Generate booking reference
        $bookingReference = 'LT-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT);
        
        // Clear session data
        unset($_SESSION['booking_data']);
        unset($_SESSION['payment_booking_data']);
        unset($_SESSION['payment_order_id']);
        
        return [
            'success' => true,
            'booking_reference' => $bookingReference,
            'booking_id' => $bookingId
        ];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($pdo)) {
            $pdo->rollback();
        }
        error_log("Booking creation error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => "Booking failed: " . $e->getMessage()
        ];
    }
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
        .qr-placeholder {
            width: 120px;
            height: 120px;
            border: 2px dashed #dee2e6;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f8f9fa;
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
                            <?php if (!empty($order_id)): ?>
                            <div class="col-md-12">
                                <strong>Transaction ID:</strong><br>
                                <code><?= htmlspecialchars($order_id) ?></code>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <div class="qr-placeholder">
                                <i class="fas fa-qrcode fa-3x text-muted"></i>
                            </div>
                            <small class="text-muted">QR Code for mobile verification</small>
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

    <!-- Confirmation Content -->
    <?php if (isset($success) && $success): ?>
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
                            <h3><i class="fas fa-bus me-2 text-primary"></i>Lanka Transit E-Ticket</h3>
                            <p class="text-muted">Present this ticket when boarding</p>
                        </div>
                        
                        <div class="route-display">
                            <?= htmlspecialchars($bookingData['origin']) ?>
                            <span class="route-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                            <?= htmlspecialchars($bookingData['destination']) ?>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user fa-2x text-primary mb-2"></i>
                                        <h6 class="card-title text-muted small">PASSENGER</h6>
                                        <p class="card-text fw-bold"><?= htmlspecialchars($bookingData['passenger_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-phone fa-2x text-success mb-2"></i>
                                        <h6 class="card-title text-muted small">PHONE</h6>
                                        <p class="card-text fw-bold"><?= htmlspecialchars($bookingData['phone']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-bus fa-2x text-warning mb-2"></i>
                                        <h6 class="card-title text-muted small">BUS</h6>
                                        <p class="card-text fw-bold"><?= htmlspecialchars($bookingData['bus_number']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chair fa-2x text-info mb-2"></i>
                                        <h6 class="card-title text-muted small">SEAT</h6>
                                        <p class="card-text fw-bold"><?= htmlspecialchars($bookingData['seat_number']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar fa-2x text-danger mb-2"></i>
                                        <h6 class="card-title text-muted small">DATE</h6>
                                        <p class="card-text fw-bold"><?= date('M j, Y', strtotime($bookingData['travel_date'])) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-2x text-secondary mb-2"></i>
                                        <h6 class="card-title text-muted small">DEPARTURE</h6>
                                        <p class="card-text fw-bold">
                                            <?= !empty($bookingData['departure_time']) ? date('g:i A', strtotime($bookingData['departure_time'])) : 'N/A' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill fa-2x text-success mb-2"></i>
                                        <h6 class="card-title text-muted small">FARE</h6>
                                        <p class="card-text fw-bold">Rs. <?= number_format($bookingData['fare'], 2) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                        <h6 class="card-title text-muted small">STATUS</h6>
                                        <p class="card-text fw-bold text-success">CONFIRMED</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="qr-placeholder">
                                <div class="text-center text-muted small">
                                    <i class="fas fa-qrcode fa-2x mb-2"></i><br>
                                    <?= $bookingReference ?>
                                </div>
                            </div>
                            <p class="text-muted small">Scan QR code for quick verification</p>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 col-lg-3">
                            <button onclick="window.print()" class="btn btn-primary w-100">
                                <i class="fas fa-print me-2"></i>Print Ticket
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="ticket_pdf.php?ref=<?= urlencode($bookingReference) ?>&name=<?= urlencode($bookingData['passenger_name']) ?>&phone=<?= urlencode($bookingData['phone']) ?>&origin=<?= urlencode($bookingData['origin']) ?>&destination=<?= urlencode($bookingData['destination']) ?>&date=<?= urlencode($bookingData['travel_date']) ?>&bus=<?= urlencode($bookingData['bus_number']) ?>&seat=<?= urlencode($bookingData['seat_number']) ?>&fare=<?= urlencode($bookingData['fare']) ?>" 
               class="btn btn-success w-100">
                                <i class="fas fa-download me-2"></i>Download PDF
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="booking.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-ticket-alt me-2"></i>Book Another
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="../index.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-home me-2"></i>Go Home
                            </a>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Important Instructions
                        </h5>
                        <ul class="mb-0">
                            <li>Arrive at the bus station at least 15 minutes before departure</li>
                            <li>Keep this ticket and a valid ID with you during travel</li>
                            <li>Boarding will start 10 minutes before departure</li>
                            <li>For any queries, call our helpline: <strong>0115-123-456</strong></li>
                        </ul>
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
                    <a href="booking.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Try Again
                    </a>
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
