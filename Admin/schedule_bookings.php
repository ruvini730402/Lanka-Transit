<?php
/**
 * Schedule Bookings View
 * Shows all bookings for a specific schedule
 */

require_once '../classes/Schedule.php';

// Check if schedule ID is provided
if (!isset($_GET['schedule_id']) || !is_numeric($_GET['schedule_id'])) {
    header('Location: schedule_listing.php');
    exit();
}

$scheduleId = (int)$_GET['schedule_id'];

try {
    $schedule = new Schedule();
    $scheduleDetails = $schedule->getScheduleById($scheduleId);
    $bookings = $schedule->getScheduleBookings($scheduleId);
    
    if (!$scheduleDetails) {
        header('Location: schedule_listing.php');
        exit();
    }
} catch (Exception $e) {
    die("Error loading schedule bookings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Schedule Bookings - <?= htmlspecialchars($scheduleDetails['BusNumber']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<div class="container mt-4">
  <!-- Back Button -->
  <a href="schedule_listing.php" class="btn btn-maroon-outline back-btn">&larr; Back to Schedules</a>

  <!-- Schedule Details Header -->
  <div class="card mb-4">
    <div class="card-header">
      <h4 class="mb-0">
        <i class="fas fa-bus"></i> Schedule Details
      </h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <strong>Bus Number:</strong><br>
          <span class="text-primary"><?= htmlspecialchars($scheduleDetails['BusNumber']) ?></span>
        </div>
        <div class="col-md-3">
          <strong>Route:</strong><br>
          <?= htmlspecialchars($scheduleDetails['Origin']) ?> → <?= htmlspecialchars($scheduleDetails['Destination']) ?>
        </div>
        <div class="col-md-3">
          <strong>Date & Time:</strong><br>
          <?= date('M d, Y', strtotime($scheduleDetails['DepartureTime'])) ?><br>
          <small class="text-muted">
            <?= date('h:i A', strtotime($scheduleDetails['DepartureTime'])) ?> - 
            <?= date('h:i A', strtotime($scheduleDetails['ArrivalTime'])) ?>
          </small>
        </div>
        <div class="col-md-3">
          <strong>Fare:</strong><br>
          <span class="text-success">LKR <?= number_format($scheduleDetails['Fare'], 2) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Bookings Section -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="fas fa-users"></i> Bookings for this Schedule
        <span class="badge bg-primary ms-2"><?= count($bookings) ?></span>
      </h5>
    </div>
    <div class="card-body">
      <?php if (empty($bookings)): ?>
      <div class="text-center py-4">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No Bookings Yet</h5>
        <p class="text-muted">This schedule doesn't have any bookings yet.</p>
      </div>
      <?php else: ?>
      
      <!-- Booking Statistics -->
      <?php 
      $confirmedBookings = array_filter($bookings, function($b) { return $b['Status'] === 'confirmed'; });
      $cancelledBookings = array_filter($bookings, function($b) { return $b['Status'] === 'cancelled'; });
      $completedBookings = array_filter($bookings, function($b) { return $b['Status'] === 'completed'; });
      $totalRevenue = array_sum(array_column($confirmedBookings, 'Fare'));
      ?>
      
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body text-center">
              <h5><?= count($confirmedBookings) ?></h5>
              <small>Confirmed</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-white">
            <div class="card-body text-center">
              <h5><?= count($completedBookings) ?></h5>
              <small>Completed</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-danger text-white">
            <div class="card-body text-center">
              <h5><?= count($cancelledBookings) ?></h5>
              <small>Cancelled</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body text-center">
              <h5>LKR <?= number_format($totalRevenue, 2) ?></h5>
              <small>Total Revenue</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Bookings Table -->
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Passenger</th>
              <th>Seat Number</th>
              <th>Phone</th>
              <th>Booking Time</th>
              <th>Fare</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $booking): ?>
            <tr>
              <td>
                <strong>#<?= $booking['ID'] ?></strong>
              </td>
              <td>
                <?php if ($booking['Username']): ?>
                  <strong><?= htmlspecialchars($booking['Username']) ?></strong>
                  <br><small class="text-muted"><?= htmlspecialchars($booking['Email']) ?></small>
                <?php else: ?>
                  <em class="text-muted">Guest Booking</em>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-secondary"><?= htmlspecialchars($booking['SeatNumber']) ?></span>
              </td>
              <td>
                <i class="fas fa-phone"></i> <?= htmlspecialchars($booking['PhoneNumber']) ?>
              </td>
              <td>
                <?= date('M d, Y', strtotime($booking['BookingTime'])) ?>
                <br><small class="text-muted"><?= date('h:i A', strtotime($booking['BookingTime'])) ?></small>
              </td>
              <td>
                <strong>LKR <?= number_format($booking['Fare'], 2) ?></strong>
              </td>
              <td>
                <?php
                $statusClass = '';
                $statusIcon = '';
                switch ($booking['Status']) {
                    case 'confirmed':
                        $statusClass = 'bg-success';
                        $statusIcon = 'fas fa-check-circle';
                        break;
                    case 'cancelled':
                        $statusClass = 'bg-danger';
                        $statusIcon = 'fas fa-times-circle';
                        break;
                    case 'completed':
                        $statusClass = 'bg-warning';
                        $statusIcon = 'fas fa-flag-checkered';
                        break;
                }
                ?>
                <span class="badge <?= $statusClass ?>">
                  <i class="<?= $statusIcon ?>"></i> <?= ucfirst($booking['Status']) ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bundle.min.js"></script>

</body>
</html>