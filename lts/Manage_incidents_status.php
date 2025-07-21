<?php
$connection = new mysqli("localhost", "root", "", "lankatrasit");
if ($connection->connect_error) {
    die("Connection failed");
}

// Filters
$statusFilter = $_GET['status'] ?? '';
$busNumberFilter = $_GET['bus_number'] ?? '';

$where = [];
if (!empty($statusFilter)) {
    $where[] = "status = '" . $connection->real_escape_string($statusFilter) . "'";
}
if (!empty($busNumberFilter)) {
    $where[] = "bus_number LIKE '%" . $connection->real_escape_string($busNumberFilter) . "%'";
}
$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT * FROM incidents $whereClause ORDER BY id DESC";
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
    :root {
      --primary: #060725; /* Navy blue for background */
      --accent: #f1424f;
      --light: #f8f8f8;
      --container-bg: #f0f0f5; /* light grayish background for message container */
    }

    body {
      background: var(--primary);
      font-family: 'Segoe UI', sans-serif;
      color: #333;
    }

    .welcome-card {
      background: var(--container-bg);
      color: var(--primary);
      border-radius: 12px;
      padding: 20px 30px;
      margin: 40px auto 20px;
      max-width: 1100px;
      display: flex;
      align-items: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      font-size: 1.1rem;
      font-weight: 500;
      /* Removed justify-content space-between since only one message */
    }

    .card-container {
      background: white;
      padding: 25px;
      margin: 0 auto 40px;
      max-width: 1100px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .form-select,
    .form-control {
      border-radius: 6px;
    }

    .btn-primary {
      background: var(--primary);
      border: none;
    }

    .btn-primary:hover {
      background: #04051a;
    }

    .status-pill {
      padding: 6px 14px;
      border-radius: 50px;
      font-size: 0.8rem;
      color: white;
      font-weight: 500;
    }

    .Pending { background: #f0ad4e; }
    .InProgress { background: #5bc0de; }
    .Resolved { background: #5cb85c; }
  </style>
</head>
<body>

  <!-- Message only, no heading -->
  <div class="welcome-card">
    <p class="mb-0">Manage &amp; update passenger-reported incidents for LankaTransit.</p>
  </div>

  <!-- Filter + Table -->
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
        <button type="submit" class="btn btn-primary">
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
          <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="10">No matching records found.</td></tr>
          <?php else: $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['tracking_id']) ?></td>
              <td><?= date('Y-m-d H:i', strtotime($row['incident_datetime'])) ?></td>
              <td><?= htmlspecialchars($row['incident_type']) ?></td>
              <td><?= htmlspecialchars($row['bus_number']) ?></td>
              <td><?= htmlspecialchars($row['route']) ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td>
                <?php if (!empty($row['attachment'])): ?>
                  <a href="<?= $row['attachment'] ?>" target="_blank">View</a>
                <?php else: ?>
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
                  <button type="submit" name="update_single" value="<?= $row['id'] ?>" class="btn btn-primary btn-sm">Update</button>
                </div>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </form>
  </div>
</body>
</html>
