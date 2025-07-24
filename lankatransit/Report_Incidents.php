<?php
// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("❌ Connection failed: Unable to connect to database");
}

// --- Fetching Incident Records ---
$incidentRows = [];

$stmt = $conn->prepare("SELECT BookingId, Description, Status, ReportedDate, ResolvedDate FROM Incident ORDER BY ReportedDate DESC");
$stmt->execute();
$incidentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close database connection
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Incident Reporting - LankaTransit</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <style>
    body {
        background-color: #800000;
        font-family: 'Segoe UI', sans-serif;
    }
    .container {
        max-width: 900px;
        margin-top: 20px;
        padding: 0 15px;
    }
    .form-section {
        background-color: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 0 12px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .form-section h3 {
        color: #800000;
    }
    
    .form-label{
        color: #800000;
        font-weight: bold;
    }
    
     
    .back-in-header {
            color: #800000;
            display: inline-flex;
            align-items: center;
        }

        .back-in-header:hover {
        color: #600000;
        }   
    .btn-custom {
        background-color: #800000;
        color: #fff;
    }
    .btn-custom:hover {
        background-color: #600000;
    }
    .status-pill {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        color: white;
        display: inline-block;
    }
    .submitted { background-color: #f0ad4e; }
    .inprogress { background-color: #5bc0de; }
    .resolved { background-color: #5cb85c; }
    .pending { background-color: #f0ad4e; } /* Added this for Pending status */
    
    /* --- Responsive Styles for smaller screens (max-width: 576px) --- */
        @media (max-width: 576px) {
            .form-section {
                padding: 20px; /* Reduce padding on smaller screens */
            }

            .back-icon {
                position: fixed; /* Fixes position on scroll */
                top: 80px;
                left: 30px;
                width: 40px; /* Smaller icon size */
                height: 40px;
                z-index: 999;
            }

            .back-icon i {
                font-size: 24px; /* Smaller icon font size */
            }

            .container {
                padding-top: 30px; /* Add padding to prevent content overlap with fixed icon */
            }
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
                        <a class="nav-link" href="UserDashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="UserFeedbackForm.php">
                            <i class="fas fa-comment-alt me-1"></i>Feedback
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="Report_Incidents.php">
                            <i class="fas fa-exclamation-triangle me-1"></i>Report Incident
                        </a>
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



<div class="container">
  <!-- Incident Report Form -->
  <div class="form-section">
    <h3>📝 Report an Incident</h3>
    <form action="submit_incidents.php" method="POST">
      <div class="mb-3">
          <br>
        <label class="form-label">Phone Number</label>
        <input type="text" class="form-control" name="phone_number" required placeholder="Enter your phone number">
      </div>

      <div class="mb-3">
        <label class="form-label">Description of the Incident</label>
        <textarea class="form-control" name="description" rows="4" required placeholder="Describe what happened in detail..."></textarea>
      </div>

        <button type="submit" class="btn btn-custom" style="background-color: #800000; color: white;">Submit Incident</button>
    </form>
  </div>

  <!-- Tracked Incidents Table -->
  <div class="form-section">
    <h3>📋 Tracked Incidents</h3>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Booking ID</th>
            <th>Description</th>
            <th>Status</th>
            <th>Reported Date</th>
            <th>Resolved Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (count($incidentRows) === 0) {
              echo "<tr><td colspan='6' class='text-center'>No incidents reported yet.</td></tr>";
          } else {
              $i = 1;
              foreach ($incidentRows as $incident) {
                  $statusClass = strtolower(str_replace(' ', '', $incident['Status']));
                  $resolved = $incident['ResolvedDate'] ?: 'N/A';
                  echo "<tr>
                      <td>{$i}</td>
                      <td>{$incident['BookingId']}</td>
                      <td>{$incident['Description']}</td>
                      <td><span class='status-pill {$statusClass}'>{$incident['Status']}</span></td>
                      <td>{$incident['ReportedDate']}</td>
                      <td>{$resolved}</td>
                    </tr>";
                  $i++;
              }
          }
          ?>
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
