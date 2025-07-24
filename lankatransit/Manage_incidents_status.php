<?php
// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

$statusFilter = trim($_GET['status'] ?? '');
$bookingIdFilter = trim($_GET['booking_id'] ?? '');

// Validate status filter
$allowedStatuses = ['', 'submitted', 'Submitted', 'In Progress', 'Resolved', 'Pending'];
if (!in_array($statusFilter, $allowedStatuses)) {
    $statusFilter = '';
}

// Sanitize booking ID filter (allow only alphanumeric and basic characters)
if (!empty($bookingIdFilter) && !preg_match('/^[A-Za-z0-9\-_]+$/', $bookingIdFilter)) {
    $bookingIdFilter = '';
}

$where = [];
$params = [];

if (!empty($statusFilter)) {
    $where[] = "Status = ?";
    $params[] = $statusFilter;
}
if (!empty($bookingIdFilter)) {
    $where[] = "BookingId LIKE ?";
    $params[] = "%" . $bookingIdFilter . "%";
}

$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
$sql = "SELECT * FROM incident $whereClause ORDER BY ReportedDate DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Incident Status - LankaTransit</title>
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
            max-width: 1100px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .card-container {
            background: #f0f0f5;
            padding: 25px;
            margin: 0 auto 40px;
            max-width: 1100px;
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
            font-size: 0.95rem;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            color: white;
            font-weight: 500;
        }

        .pending { background: #f0ad4e; }
        .inprogress { background: #5bc0de; }
        .resolved { background: #5cb85c; }
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
                        <a class="nav-link" href="Logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


<div class="page-wrapper">
    <div class="welcome-card">
        <p class="mb-0">Manage &amp; update passenger-reported incidents for LankaTransit.</p>
    </div>

    <div class="card-container">
        <!-- Filter Form -->
        <form class="row mb-4 g-2 align-items-center" method="GET">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Pending" <?= ($statusFilter == "Submitted") ? "selected" : "" ?>>Submitted</option>
                    <option value="In Progress" <?= ($statusFilter == "In Progress") ? "selected" : "" ?>>In Progress</option>
                    <option value="Resolved" <?= ($statusFilter == "Resolved") ? "selected" : "" ?>>Resolved</option>
                </select>
            </div>

           

            <div class="col-md-auto">
                <button type="submit" class="btn" style="background-color: #800000; color: white;">
                    <i class="bi bi-filter-circle me-1"></i> Apply Filters
                </button>
            </div>

            <div class="col-md-auto">
                <a href="Manage_incidents_status.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <!-- Incidents Table -->
        <form method="POST" action="update_incidents_status.php">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Booking ID</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Reported Date</th>
                        <th>Resolved Date</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result)): ?>
                        <tr><td colspan="7">No matching records found.</td></tr>
                    <?php else:
                        $i = 1;
                        foreach ($result as $row):
                            $statusClass = strtolower(str_replace(' ', '', htmlspecialchars($row['Status'], ENT_QUOTES, 'UTF-8')));
                            $resolvedDate = $row['ResolvedDate'] ?? '-';
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['BookingId']) ?></td>
                            <td style="white-space: normal; text-align: left;"><?= nl2br(htmlspecialchars($row['Description'])) ?></td>
                            <td><span class="status-pill <?= $statusClass ?>"><?= htmlspecialchars($row['Status']) ?></span></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['ReportedDate']))) ?></td>
                            <td><?= $row['ResolvedDate'] ? htmlspecialchars(date('Y-m-d', strtotime($row['ResolvedDate']))) : '-' ?></td>

                            <td>
                                <input type="hidden" name="booking_ids[]" value="<?= htmlspecialchars($row['BookingId'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <select name="new_statuses[]" class="form-select form-select-sm">
                                        <option <?= $row['Status'] === "submitted" ? "selected" : "" ?>>Submitted</option>
                                        <option <?= $row['Status'] === "In Progress" ? "selected" : "" ?>>In Progress</option>
                                        <option <?= $row['Status'] === "Resolved" ? "selected" : "" ?>>Resolved</option>
                                    </select>
                                    <button type="submit" name="update_single" value="<?= htmlspecialchars($row['BookingId'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm" style="background-color: #800000; color: white;">Update</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </form>
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
