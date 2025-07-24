<?php
// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

$statusFilter = $_GET['status'] ?? '';
$bookingIdFilter = $_GET['booking_id'] ?? '';

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
<?php include 'Header.php'; ?>

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
                            $statusClass = strtolower(str_replace(' ', '', $row['Status']));
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
                                <input type="hidden" name="booking_ids[]" value="<?= $row['BookingId'] ?>">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <select name="new_statuses[]" class="form-select form-select-sm">
                                        <option <?= $row['Status'] === "submitted" ? "selected" : "" ?>>Submitted</option>
                                        <option <?= $row['Status'] === "In Progress" ? "selected" : "" ?>>In Progress</option>
                                        <option <?= $row['Status'] === "Resolved" ? "selected" : "" ?>>Resolved</option>
                                    </select>
                                    <button type="submit" name="update_single" value="<?= $row['BookingId'] ?>" class="btn btn-sm" style="background-color: #800000; color: white;">Update</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<?php include 'Footer.php'; ?>
</body>
</html>
