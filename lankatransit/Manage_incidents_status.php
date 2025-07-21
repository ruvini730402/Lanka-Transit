<?php
$connection = new mysqli("localhost", "root", "", "lankatrasit");

// Check if the database connection failed.
if ($connection->connect_error) {
    die("Connection failed");
}

// --- Incident Filtering Logic ---

// Get filter values from the URL query parameters (GET request).
// Uses the null coalescing operator (??) to set a default empty string if the parameter is not present.
$statusFilter = $_GET['status'] ?? '';
$busNumberFilter = $_GET['bus_number'] ?? '';

// Initialize an array to hold WHERE clause conditions.
$where = [];

// Add status filter condition if provided.
if (!empty($statusFilter)) {
    // Escape the status string to prevent SQL injection and add to the conditions array.
    $where[] = "status = '" . $connection->real_escape_string($statusFilter) . "'";
}

// Add bus number filter condition if provided.
if (!empty($busNumberFilter)) {
    // Escape the bus number string and use LIKE for partial matching, then add to conditions.
    $where[] = "bus_number LIKE '%" . $connection->real_escape_string($busNumberFilter) . "%'";
}

// Construct the full WHERE clause.
// If there are conditions, prepend "WHERE" and join them with " AND ". Otherwise, it's an empty string.
$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// SQL query to select all incidents from the 'incidents' table.
// Includes the constructed WHERE clause for filtering and orders results by ID in descending order.
$sql = "SELECT * FROM incidents $whereClause ORDER BY id DESC";

// Execute the SQL query.
$result = $connection->query($sql);
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
        /* CSS Variables for consistent theming */
        :root {
            --accent: #f1424f;
            --container-bg: #f0f0f5; /* Light grayish background for cards/containers */
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        /* Styling for the welcome message card at the top */
        .welcome-card {
            background: var(--container-bg);
            color: var(--primary);
            border-radius: 12px;
            padding: 20px 30px;
            margin: 40px auto 20px; /* Centers the card horizontally */
            max-width: 1100px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Styling for the main content card containing filters and table */
        .card-container {
            background: white;
            padding: 25px;
            margin: 0 auto 40px; /* Centers the card horizontally */
            max-width: 1100px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* General styling for Bootstrap form elements */
        .form-select,
        .form-control {
            border-radius: 6px;
        }

        /* Styling for Bootstrap primary buttons */
        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background: #04051a;
        }

        /* Styling for status pills (small colored labels) */
        .status-pill {
            padding: 6px 14px;
            border-radius: 50px; /* Makes it pill-shaped */
            font-size: 0.8rem;
            color: white;
            font-weight: 500;
        }

        /* Specific background colors for different status types */
        .Pending { background: #f0ad4e; } /* Orange for Pending */
        .InProgress { background: #5bc0de; } /* Blue for InProgress */
        .Resolved { background: #5cb85c; } /* Green for Resolved */
    </style>
</head>
<body>
    <?php
    // Include the common header for the page.
    include 'Header.php';
    ?>
    <div class="welcome-card">
        <p class="mb-0">Manage &amp; update passenger-reported incidents for LankaTransit.</p>
    </div>

    <div class="card-container">
        <form class="row mb-4 g-2 align-items-center" method="GET">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Pending" <?= ($statusFilter == "Pending") ? "selected" : "" ?>>Pending</option>
                    <option value="In Progress" <?= ($statusFilter == "In Progress") ? "selected" : "" ?>>In Progress</option>
                    <option value="Resolved" <?= ($statusFilter == "Resolved") ? "selected" : "" ?>>Resolved</option>
                </select>
            </div>

            <div class="col-md-4">
                <input type="text" name="bus_number" value="<?= htmlspecialchars($busNumberFilter) ?>" class="form-control" placeholder="Filter by Bus No.">
            </div>

            <div class="col-md-auto">
                <button type="submit" class="btn" style="background-color: #800000; color: white; border: none;">
                    <i class="bi bi-filter-circle me-1"></i> Apply Filters
                </button>
            </div>

            <div class="col-md-auto">
                <a href="manage_incident_status.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <form method="POST" action="update_incident_status.php">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tracking ID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Bus No.</th>
                        <th>Route</th>
                        <th>Location</th>
                        <th>Attachment</th>
                        <th>Status</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are no rows returned from the database query.
                    if ($result->num_rows === 0):
                    ?>
                        <tr><td colspan="10">No matching records found.</td></tr>
                    <?php
                    else:
                        $i = 1; // Initialize counter for row numbering.
                        // Loop through each row fetched from the database result.
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <tr>
                                <td><?= $i++ ?></td> <td><?= htmlspecialchars($row['tracking_id']) ?></td> <td><?= date('Y-m-d H:i', strtotime($row['incident_datetime'])) ?></td> <td><?= htmlspecialchars($row['incident_type']) ?></td>
                                <td><?= htmlspecialchars($row['bus_number']) ?></td>
                                <td><?= htmlspecialchars($row['route']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td>
                                    <?php
                                    // Check if an attachment path exists.
                                    if (!empty($row['attachment'])):
                                    ?>
                                        <a href="<?= $row['attachment'] ?>" target="_blank">View</a>
                                    <?php
                                    else:
                                    ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-pill <?= str_replace(' ', '', $row['status']) ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <input type="hidden" name="incident_ids[]" value="<?= $row['id'] ?>">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <select name="new_statuses[]" class="form-select form-select-sm">
                                            <option <?= ($row['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                                            <option <?= ($row['status'] === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                            <option <?= ($row['status'] === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" name="update_single" value="<?= $row['id'] ?>" class="btn btn-sm" style="background-color: #800000; color: white; border: none;">Update</button>
                                    </div>
                                </td>
                            </tr>
                    <?php
                        endwhile; // End of while loop
                    endif; // End of if ($result->num_rows === 0)
                    ?>
                </tbody>
            </table>
        </form>
    </div>
    <?php
    include 'Footer.php';
    ?>
</body>
</html>