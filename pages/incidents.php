<?php
require_once __DIR__ . "/../includes/session_config.php";

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
    die("Unable to connect to our system. Please try again later.");
}

// Get username from database for sidebar
$stmt = $conn->prepare("SELECT Name, PhoneNumber FROM User WHERE ID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['Name']) : 'User';
$userPhoneNumber = $user['PhoneNumber'] ?? null;

// --- Fetching User's Bookings for Trip Selection ---
$userBookings = [];
if ($userPhoneNumber) {
    $stmt = $conn->prepare("
        SELECT b.ID, b.BookingTime, b.TravelDate, b.SeatNumber, b.Fare, b.Origin, b.Destination,
               bus.BusNumber
        FROM Booking b
        LEFT JOIN Bus bus ON b.BusID = bus.ID
        WHERE b.PhoneNumber = ? 
          AND b.Status IN ('confirmed', 'completed')
          AND b.TravelDate <= CURDATE()
        ORDER BY b.TravelDate DESC, b.BookingTime DESC
        LIMIT 20
    ");
    $stmt->execute([$userPhoneNumber]);
    $userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log for debugging
    error_log("DEBUG: Found " . count($userBookings) . " PAST bookings for incidents, phone " . $userPhoneNumber);
}

// --- Fetching Incident Records for Current User ---
$incidentRows = [];

if ($userPhoneNumber) {
    $stmt = $conn->prepare("
        SELECT i.ID, i.BookingId, i.Description, i.Status, i.ReportedDate, i.ResolvedDate,
               b.TravelDate, b.Origin, b.Destination, b.SeatNumber, bus.BusNumber
        FROM Incident i
        JOIN Booking b ON i.BookingId = b.ID
        LEFT JOIN Bus bus ON b.BusID = bus.ID
        WHERE b.PhoneNumber = ?
        ORDER BY i.ID DESC
    ");
    $stmt->execute([$userPhoneNumber]);
    $incidentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log for debugging
    error_log("DEBUG: Found " . count($incidentRows) . " incidents for user phone " . $userPhoneNumber);
}

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
  <link rel="stylesheet" href="../assets/css/user-incidents.css" />

    
  
</head>
<body>
  <div class="container">
    <!-- Top Navigation for Mobile -->
    <div class="top-nav">
      <div class="logo-section">
        <a href="../index.php" style="color: white; text-decoration: none; font-weight: bold; font-size: 18px;">LankaTransit</a>
      </div>
      <div class="user-dropdown">
        <div class="user-section" onclick="toggleDropdown()">
          <img src="../assets/images/uploads/rosalette.jpg" alt="User Icon">
          <span><?= $username ?></span>
          <i class="fas fa-caret-down"></i>
        </div>
        <div class="dropdown-content">
          <a href="../auth/Logout.php">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </a>
        </div>
      </div>
    </div>
    
    <!-- Navigation Tabs for Mobile -->
    <div class="nav-tabs">
      <a href="dashboard.php">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
      <a href="incidents.php" class="active">
        <i class="fas fa-exclamation-triangle"></i> Report Incident
      </a>
      <a href="cancel_booking.php">
        <i class="fas fa-times-circle"></i> Cancel Booking
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
          <li class="active"><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
          <li><a href="cancel_booking.php"><i class="fas fa-times-circle"></i> Cancel Booking</a></li>
        </ul>
      </nav>
      <div class="logout">
        <a href="../auth/Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
    
    <div class="main-content">

  <!-- Incident Report Form Container -->
  <div class="report-form-container">
    <div class="form-section">
      <h3>📝 Report an Incident</h3>
      <form action="submit_incidents.php" method="POST">
        <div class="mb-3">
          <label class="form-label"><strong>Select Trip</strong></label>
          <div style="position: relative;">
            <select class="form-control" name="booking_id" required style="appearance: none; background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 4 5%22><path fill=%22%23666%22 d=%22M2 0L0 2h4zm0 5L0 3h4z%22/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 12px; padding-right: 40px; color: #6c757d;">
              <option value="" disabled selected hidden>Select a completed trip to report incident</option>
              <?php if (!empty($userBookings)): ?>
                <?php foreach ($userBookings as $booking): ?>
                  <option value="<?= htmlspecialchars($booking['ID']) ?>" style="color: #333;">
                    🚌 Bus <?= htmlspecialchars($booking['BusNumber'] ?? 'N/A') ?> | 
                    <?= htmlspecialchars($booking['Origin'] ?? 'N/A') ?> → 
                    <?= htmlspecialchars($booking['Destination'] ?? 'N/A') ?> | 
                    <?= date('M j, Y', strtotime($booking['TravelDate'])) ?> | 
                    Seat <?= htmlspecialchars($booking['SeatNumber']) ?> | 
                    LKR <?= number_format($booking['Fare'], 2) ?>
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="" disabled>No completed trips found. Incidents can only be reported for past journeys.</option>
              <?php endif; ?>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label"><strong>Description of the Incident</strong></label>
          <textarea class="form-control" name="description" rows="4" required placeholder="Describe what happened in detail..."></textarea>
        </div>
          <button type="submit" class="btn btn-custom" style="background-color: #800000; color: white;">Submit Incident</button>
      </form>
    </div>
  </div>

  <!-- Tracked Incidents Table Container -->
  <div class="tracked-incidents-container">
    <div class="form-section">
      <h3>📋 Tracked Incidents</h3>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Incident ID</th>
            <th>Trip Details</th>
            <th>Description</th>
            <th>Status</th>
            <th>Reported Date</th>
            <th>Resolved Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (count($incidentRows) === 0) {
              echo "<tr><td colspan='7' class='text-center'>No incidents reported yet. Report an incident for any issues during your completed trips.</td></tr>";
          } else {
              $i = 1;
              foreach ($incidentRows as $incident) {
                  $statusClass = strtolower(str_replace(' ', '', htmlspecialchars($incident['Status'], ENT_QUOTES, 'UTF-8')));
                  $resolved = $incident['ResolvedDate'] ? date('M j, Y', strtotime($incident['ResolvedDate'])) : 'N/A';
                  $bookingId = $incident['BookingId'] ? htmlspecialchars($incident['BookingId'], ENT_QUOTES, 'UTF-8') : 'N/A';
                  $description = htmlspecialchars($incident['Description'], ENT_QUOTES, 'UTF-8');
                  $status = htmlspecialchars($incident['Status'], ENT_QUOTES, 'UTF-8');
                  $reportedDate = date('M j, Y', strtotime($incident['ReportedDate']));
                  $incidentId = htmlspecialchars($incident['ID'], ENT_QUOTES, 'UTF-8');
                  
                  // Trip details
                  $busNumber = htmlspecialchars($incident['BusNumber'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                  $origin = htmlspecialchars($incident['Origin'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                  $destination = htmlspecialchars($incident['Destination'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                  $travelDate = $incident['TravelDate'] ? date('M j, Y', strtotime($incident['TravelDate'])) : 'N/A';
                  $seatNumber = htmlspecialchars($incident['SeatNumber'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                  
                  echo "<tr>
                      <td>{$i}</td>
                      <td><strong>INC-{$incidentId}</strong><br><small class='text-muted'>Booking: {$bookingId}</small></td>
                      <td>
                        <div class='trip-info'>
                          <strong>Bus {$busNumber}</strong><br>
                          <span class='text-muted'>{$origin} → {$destination}</span><br>
                          <small class='text-muted'>Travel: {$travelDate} | Seat: {$seatNumber}</small>
                        </div>
                      </td>
                      <td><div class='incident-description'>{$description}</div></td>
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
  </div>

    </div> <!-- End main-content -->
  </div> <!-- End container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user-dashboard.js"></script>
<script>
// Dropdown functionality
function toggleDropdown() {
    document.querySelector('.user-dropdown').classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.user-section') && !event.target.closest('.user-section')) {
        var dropdown = document.querySelector('.user-dropdown');
        if (dropdown && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
}
</script>
</body>
</html>