<?php
include('dbcon.php');
// Fetch Booking Details
$bookingStmt = $connection->prepare("
    SELECT b.ID, b.SeatNumber, u.Name, u.Email, u.PhoneNumber, u.Role, b.BookingTime, u.ID AS UserID
    FROM Booking b
    LEFT JOIN User u ON b.UserId = u.ID
    ORDER BY b.BookingTime DESC
");
$bookingStmt->execute();
$bookings = $bookingStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Payment Details
$paymentStmt = $connection->prepare("
    SELECT p.ID, u.Name, u.Email, u.ID AS UserID, b.SeatNumber, b.BookingTime, p.Amount, p.PaymentMethod, p.PaymentDate, u.PhoneNumber
    FROM Payment p
    LEFT JOIN Booking b ON p.BookingId = b.ID
    LEFT JOIN User u ON b.UserId = u.ID
    ORDER BY p.PaymentDate DESC
");
$paymentStmt->execute();
$payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .back-btn {
            border: 2px solid #000080;
            color: #000080;
            font-weight: 500;
            background-color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #000080;
            color: white;
        }
    </style>
</head>
<body class="container mt-4">

    <!-- Back Button -->
    <a href="admin.html" class="back-btn mb-4 d-inline-block">&larr; Back</a>

    <!-- Page Heading -->
    <h2 class="text-center mb-5">Reservation Overview</h2>

    <div class="row g-4">
        <!-- Booking Details Card -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Seat</th>
                                    <th>Name</th>
                                    <th>NIC</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Booked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookings)): ?>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['ID']) ?></td>
                                            <td><?= htmlspecialchars($booking['SeatNumber']) ?></td>
                                            <td><?= htmlspecialchars($booking['Name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($booking['UserID']) ?></td>
                                            <td><?= htmlspecialchars($booking['PhoneNumber'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($booking['Role'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($booking['BookingTime']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">No bookings found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details Card -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>NIC</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Paid At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($payments)): ?>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($payment['ID']) ?></td>
                                            <td><?= htmlspecialchars($payment['Name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($payment['UserID']) ?></td>
                                            <td>Rs. <?= htmlspecialchars($payment['Amount']) ?></td>
                                            <td><?= htmlspecialchars($payment['PaymentMethod']) ?></td>
                                            <td><?= htmlspecialchars($payment['PaymentDate']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">No payments found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
