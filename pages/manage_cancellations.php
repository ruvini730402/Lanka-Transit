<?php
// Include database configuration
require_once '../classes/Database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

$statusFilter = trim($_GET['status'] ?? '');
$bookingIdFilter = trim($_GET['booking_id'] ?? '');
$userFilter = trim($_GET['user'] ?? '');

// Validate status filter - match BookingCancellation status values from schema_4.sql
$allowedStatuses = ['', 'pending', 'refunded', 'declined'];
if (!in_array($statusFilter, $allowedStatuses)) {
    $statusFilter = '';
}

// Sanitize filters
if (!empty($bookingIdFilter) && !preg_match('/^[A-Za-z0-9\-_]+$/', $bookingIdFilter)) {
    $bookingIdFilter = '';
}

$where = [];
$params = [];

if (!empty($statusFilter)) {
    $where[] = "bc.Status = ?";
    $params[] = $statusFilter;
}
if (!empty($bookingIdFilter)) {
    $where[] = "bc.BookingID LIKE ?";
    $params[] = "%" . $bookingIdFilter . "%";
}
if (!empty($userFilter)) {
    $where[] = "(u.Name LIKE ? OR u.PhoneNumber LIKE ?)";
    $params[] = "%" . $userFilter . "%";
    $params[] = "%" . $userFilter . "%";
}

$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Get all cancellation requests with booking and user details
$sql = "SELECT 
            bc.ID as cancellation_id,
            bc.BookingID,
            bc.CancellationReason,
            bc.RequestedAt,
            bc.Status as cancellation_status,
            bc.ProcessedAt,
            bc.UserID,
            u.Name as user_name,
            u.PhoneNumber,
            b.TravelDate,
            b.Origin,
            b.Destination,
            b.SeatNumber,
            b.Fare,
            bus.BusNumber
        FROM BookingCancellation bc
        JOIN Booking b ON bc.BookingID = b.ID
        JOIN User u ON bc.UserID = u.ID
        LEFT JOIN Bus bus ON b.BusID = bus.ID
        $whereClause
        ORDER BY bc.RequestedAt DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Booking Cancellations - LankaTransit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #f1424f;
            --container-bg: #f0f0f5;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        .welcome-card {
            background: var(--container-bg);
            border-radius: 12px;
            padding: 20px 30px;
            margin: 40px auto 20px;
            max-width: 1200px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .card-container {
            background: #f0f0f5;
            padding: 25px;
            margin: 0 auto 40px;
            max-width: 1200px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .form-select, .form-control {
            border-radius: 6px;
        }

        .table th, .table td {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table td.text-left {
            text-align: left !important;
            white-space: normal;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            color: white;
            font-weight: 500;
        }

        .pending { background: #f0ad4e; }
        .refunded { background: #5cb85c; }
        .declined { background: #d9534f; }

        .trip-details {
            font-size: 0.85rem;
            line-height: 1.3;
        }

        .user-info {
            font-size: 0.85rem;
            line-height: 1.3;
        }

        .reason-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .reason-text:hover {
            white-space: normal;
            overflow: visible;
        }

        .update-section {
            min-width: 200px;
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
                        <a class="nav-link" href="manage_incidents.php">Manage Incidents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/Logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="welcome-card">
            <p class="mb-0"><i class="fas fa-times-circle me-2"></i>Manage passenger booking cancellation requests for LankaTransit.</p>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="max-width: 1200px; margin: 0 auto 20px;">
                <i class="fas fa-check-circle me-2"></i>Cancellation status updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width: 1200px; margin: 0 auto 20px;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php
                switch ($_GET['error']) {
                    case 'invalid_id':
                        echo 'Invalid cancellation ID provided.';
                        break;
                    case 'no_status':
                        echo 'Please select a status before updating.';
                        break;
                    case 'invalid_status':
                        echo 'Invalid status selected.';
                        break;
                    case 'db_error':
                        echo 'Database error occurred. Please try again.';
                        break;
                    case 'not_found':
                        echo 'Cancellation request not found in database.';
                        break;
                    case 'update_failed':
                        echo 'Failed to update cancellation status.';
                        break;
                    case 'column_error':
                        echo 'Database column error. Please contact administrator.';
                        break;
                    case 'table_error':
                        echo 'Database table error. Please contact administrator.';
                        break;
                    case 'general_error':
                        echo 'A general error occurred. Please try again.';
                        break;
                    case 'id_not_found':
                        echo 'Cancellation ID not found in submitted data.';
                        break;
                    default:
                        echo 'An error occurred while processing your request.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card-container">
            <!-- Filter Form -->
            <form class="row mb-4 g-2 align-items-center" method="GET">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" <?= ($statusFilter == "pending") ? "selected" : "" ?>>Pending</option>
                        <option value="refunded" <?= ($statusFilter == "refunded") ? "selected" : "" ?>>Refunded</option>
                        <option value="declined" <?= ($statusFilter == "declined") ? "selected" : "" ?>>Declined</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="text" name="booking_id" class="form-control" placeholder="Booking ID" value="<?= htmlspecialchars($bookingIdFilter) ?>">
                </div>

                <div class="col-md-2">
                    <input type="text" name="user" class="form-control" placeholder="User/Phone" value="<?= htmlspecialchars($userFilter) ?>">
                </div>

                <div class="col-md-auto">
                    <button type="submit" class="btn" style="background-color: #800000; color: white;">
                        <i class="bi bi-filter-circle me-1"></i> Apply Filters
                    </button>
                </div>

                <div class="col-md-auto">
                    <a href="manage_cancellations.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <!-- Summary Cards -->
            <?php
            $pending = count(array_filter($result, fn($r) => $r['cancellation_status'] == 'pending'));
            $refunded = count(array_filter($result, fn($r) => $r['cancellation_status'] == 'refunded'));
            $declined = count(array_filter($result, fn($r) => $r['cancellation_status'] == 'declined'));
            ?>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning"><?= $pending ?></h5>
                            <p class="card-text">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $refunded ?></h5>
                            <p class="card-text">Refunded</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-danger"><?= $declined ?></h5>
                            <p class="card-text">Declined</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Requests Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Request ID</th>
                            <th>User Details</th>
                            <th>Trip Details</th>
                            <th>Cancellation Reason</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Change Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($result)): ?>
                            <tr><td colspan="8">No cancellation requests found.</td></tr>
                        <?php else:
                            $i = 1;
                            foreach ($result as $row):
                                $statusClass = strtolower(str_replace(' ', '', htmlspecialchars($row['cancellation_status'], ENT_QUOTES, 'UTF-8')));
                        ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td>
                                    <strong>CR-<?= htmlspecialchars($row['cancellation_id']) ?></strong><br>
                                    <small class="text-muted">Booking: <?= htmlspecialchars($row['BookingID']) ?></small>
                                </td>
                                <td class="text-left">
                                    <div class="user-info">
                                        <strong><?= htmlspecialchars($row['user_name']) ?></strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($row['PhoneNumber']) ?></span><br>
                                        <small class="text-muted">ID: <?= htmlspecialchars($row['UserID']) ?></small>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="trip-details">
                                        <strong>Bus <?= htmlspecialchars($row['BusNumber'] ?? 'N/A') ?></strong><br>
                                        <span class="text-muted">
                                            <?= htmlspecialchars($row['Origin'] ?? 'N/A') ?> → <?= htmlspecialchars($row['Destination'] ?? 'N/A') ?>
                                        </span><br>
                                        <small class="text-muted">
                                            Travel: <?= date('M j, Y', strtotime($row['TravelDate'])) ?><br>
                                            Seat: <?= htmlspecialchars($row['SeatNumber']) ?> | 
                                            Fare: Rs. <?= number_format($row['Fare'], 2) ?>
                                        </small>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="reason-text" title="<?= htmlspecialchars($row['CancellationReason']) ?>">
                                        <?= htmlspecialchars($row['CancellationReason']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill <?= $statusClass ?>">
                                        <?= ucfirst($row['cancellation_status']) ?>
                                    </span>
                                    <?php if ($row['ProcessedAt']): ?>
                                        <br><small class="text-muted">
                                            Processed: <?= date('M j', strtotime($row['ProcessedAt'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('M j, Y', strtotime($row['RequestedAt'])) ?><br>
                                    <small class="text-muted"><?= date('g:i A', strtotime($row['RequestedAt'])) ?></small>
                                </td>
                                <td class="update-section">
                                    <form method="POST" action="update_cancellations.php" class="d-flex flex-column gap-2">
                                        <input type="hidden" name="cancellation_id" value="<?= htmlspecialchars($row['cancellation_id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <select name="new_status" class="form-select form-select-sm" required>
                                            <option value="">Select Status</option>
                                            <option value="pending" <?= $row['cancellation_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="refunded" <?= $row['cancellation_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                            <option value="declined" <?= $row['cancellation_status'] === 'declined' ? 'selected' : '' ?>>Declined</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm" style="background-color: #800000; color: white;">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-white py-4 mt-5" style="background-color: #800000;">
        <div class="container">
            <div class="row">
                <p>&copy; 2025 Transit. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>