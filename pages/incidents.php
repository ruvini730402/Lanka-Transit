<?php
session_start();

// Include database configuration
require_once '../classes/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?error=login_required');
    exit();
}

$userId = $_SESSION['user_id'];

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("❌ Connection failed: Unable to connect to database");
}

// Get username from database for sidebar
$stmt = $conn->prepare("SELECT Name FROM User WHERE ID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['Name']) : 'User';

// --- Fetching Incident Records ---
$incidentRows = [];

$stmt = $conn->prepare("SELECT ID, BookingId, Description, Status, ReportedDate, ResolvedDate FROM Incident ORDER BY ID DESC");
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
  <link rel="stylesheet" href="../assets/css/user-dashboard.css" />
  <style>
    body {
        background-color: #ffffff;
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
    .pending { background-color: #f0ad4e; }
    
    /* Override dashboard CSS for proper layout */
    body {
      display: block !important;
      min-height: 100vh;
      margin: 0;
      overflow-x: hidden;
    }
    
    .container {
      display: block !important;
      margin-left: 250px !important;
      min-height: 100vh;
      width: calc(100% - 250px) !important;
      padding: 0 !important;
      max-width: none !important;
    }
    
    .main-content {
      height: auto !important;
      min-height: calc(100vh - 100px);
      padding: 20px !important;
      margin: 0 !important;
      width: 100%;
      overflow-x: hidden;
    }
    
    /* Responsive Styles */
    @media (max-width: 576px) {
        .form-section {
            padding: 20px;
        }
        .container {
            padding-top: 30px;
        }
    }
  </style>
    
    .form-section {
      margin-left: 0 !important;
      margin-top: 0 !important;
      margin-bottom: 0 !important;
    }
    
    /* Remove all container padding and margins */
    .container {
      max-width: none !important;
      margin-top: 0 !important;
      padding: 0 !important;
    }
    
    /* Ensure content starts at the edge */
    div.container:not(.sidebar .container) {
      padding-left: 0 !important;
      padding-right: 0 !important;
      padding-top: 0 !important;
      margin: 0 !important;
    }
    
    /* Footer positioning fix */
    footer {
      position: relative !important;
      clear: both !important;
      width: 100% !important;
      margin-left: 0 !important;
      z-index: 1 !important;
    }
    
    .footer-container {
      margin-left: 250px;
      padding: 20px;
    }
    
    @media (max-width: 768px) {
      .footer-container {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Add back navigation -->
    <div class="back-navigation" style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
      <a href="../index.php" class="back-btn" style="display: inline-flex; align-items: center; padding: 8px 12px; background: #800000; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;">
        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
        Back to Home
      </a>
    </div>
    
    <div class="sidebar">
      <div class="logo">
        <a href="../index.php">
          <img src="../assets/images/uploads/dd.png" alt="LankaTransit Logo">
        </a>
      </div>
      <hr style="height: 1px; background-color: #ccc; border: none; width: 100%; margin: 1px auto 15px auto;">

      <div class="user-profile">
        <div class="user-icon">
          <img src="../assets/images/uploads/rosalette.jpg" alt="User Icon">
        </div>
        <div class="user-info mobile-only">
          <p class="username"><?= $username ?></p>
          <button class="dropdown-toggle"><i class="fas fa-caret-down"></i></button>
          <div class="dropdown-menu">
            <a href="../auth/Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
      <nav class="navigation">
        <ul>
          <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
          <li class="active"><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
        </ul>
      </nav>
      <div class="logout">
        <a href="../auth/Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
    
    <div class="main-content">

  <!-- Incident Report Form -->
  <div class="form-section">
    <h3>📝 Report an Incident</h3>
    <form action="submit_incidents.php" method="POST">
      <div class="mb-3">
        <br>
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
            <th>Incident ID</th>
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
              echo "<tr><td colspan='7' class='text-center'>No incidents reported yet.</td></tr>";
          } else {
              $i = 1;
              foreach ($incidentRows as $incident) {
                  $statusClass = strtolower(str_replace(' ', '', htmlspecialchars($incident['Status'], ENT_QUOTES, 'UTF-8')));
                  $resolved = $incident['ResolvedDate'] ? htmlspecialchars($incident['ResolvedDate'], ENT_QUOTES, 'UTF-8') : 'N/A';
                  $bookingId = $incident['BookingId'] ? htmlspecialchars($incident['BookingId'], ENT_QUOTES, 'UTF-8') : 'N/A';
                  $description = htmlspecialchars($incident['Description'], ENT_QUOTES, 'UTF-8');
                  $status = htmlspecialchars($incident['Status'], ENT_QUOTES, 'UTF-8');
                  $reportedDate = htmlspecialchars($incident['ReportedDate'], ENT_QUOTES, 'UTF-8');
                  $incidentId = htmlspecialchars($incident['ID'], ENT_QUOTES, 'UTF-8');
                  
                  echo "<tr>
                      <td>{$i}</td>
                      <td>INC-{$incidentId}</td>
                      <td>{$bookingId}</td>
                      <td>{$description}</td>
                      <td><span class='status-pill {$statusClass}'>{$status}</span></td>
                      <td>{$reportedDate}</td>
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

    </div> <!-- End main-content -->
  </div> <!-- End container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user-dashboard.js"></script>
</body>
</html>
