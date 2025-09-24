<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Schedule.php';

try {
    $database = new Database();
    $connection = $database->getConnection();
    $scheduleObj = new Schedule($connection);
    
    // Handle search
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    if ($searchTerm) {
        $schedules = $scheduleObj->searchSchedules($searchTerm);
    } else {
        $schedules = $scheduleObj->getAllSchedules();
    }
    
    // Get statistics
    $statistics = $scheduleObj->getScheduleStatistics();
    
    // Get buses for schedule creation
    $buses = $scheduleObj->getBusesForScheduling();
} catch (PDOException $e) {
    die("Error fetching schedules: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Schedule Management - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <style>
    .schedule-badge {
      font-size: 0.8em;
      padding: 0.25em 0.5em;
    }
    .past-schedule {
      opacity: 0.7;
      background-color: #f8f9fa;
    }
    .today-schedule {
      background-color: #fff3cd;
    }
    .future-schedule {
      background-color: #d1ecf1;
    }
    .stat-card {
      border-left: 4px solid #800000;
      transition: transform 0.2s;
    }
    .stat-card:hover {
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <!-- Back Button -->
  <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>

  <!-- Page Title -->
  <h1 class="text-center fw-bold mb-4">Schedule Management</h1>

  <!-- Success/Error Messages -->
  <?php if (isset($_GET['msg'])): ?>
      <?php
      $msg = $_GET['msg'];
      $isError = isset($_GET['type']) && $_GET['type'] === 'error';
      $alertClass = $isError ? 'alert-danger' : 'alert-success';
      ?>
      <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  <?php endif; ?>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body text-center">
          <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
          <h5 class="card-title"><?= number_format($statistics['total_schedules']) ?></h5>
          <p class="card-text text-muted">Total Schedules</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body text-center">
          <i class="fas fa-clock fa-2x text-success mb-2"></i>
          <h5 class="card-title"><?= number_format($statistics['active_schedules']) ?></h5>
          <p class="card-text text-muted">Active Schedules</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body text-center">
          <i class="fas fa-calendar-day fa-2x text-warning mb-2"></i>
          <h5 class="card-title"><?= number_format($statistics['today_schedules']) ?></h5>
          <p class="card-text text-muted">Today's Schedules</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body text-center">
          <i class="fas fa-bus fa-2x text-info mb-2"></i>
          <h5 class="card-title"><?= number_format($statistics['scheduled_buses']) ?></h5>
          <p class="card-text text-muted">Scheduled Buses</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Add Section -->
  <div class="row mb-4">
    <div class="col-md-8">
      <form method="GET" class="d-flex">
        <input type="text" name="search" class="form-control me-2" 
               placeholder="Search by bus number, route, or date..." 
               value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit" class="btn btn-outline-primary">
          <i class="fas fa-search"></i> Search
        </button>
        <?php if ($searchTerm): ?>
        <a href="schedule_listing.php" class="btn btn-outline-secondary ms-2">
          <i class="fas fa-times"></i> Clear
        </a>
        <?php endif; ?>
      </form>
    </div>
    <div class="col-md-4 text-end">
      <button type="button" class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
        <i class="fas fa-plus"></i> Add New Schedule
      </button>
    </div>
  </div>

  <!-- Schedules Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="fas fa-list"></i> Schedule List 
        <?php if ($searchTerm): ?>
        <small class="text-muted">(Filtered by: "<?= htmlspecialchars($searchTerm) ?>")</small>
        <?php endif; ?>
      </h5>
    </div>
    <div class="card-body">
      <?php if (empty($schedules)): ?>
      <div class="text-center py-4">
        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No schedules found</h5>
        <p class="text-muted">
          <?= $searchTerm ? 'Try adjusting your search criteria.' : 'Click "Add New Schedule" to create the first schedule.' ?>
        </p>
      </div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Bus Number</th>
              <th>Route</th>
              <th>Date</th>
              <th>Departure</th>
              <th>Arrival</th>
              <th>Fare (LKR)</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($schedules as $schedule): 
              $departureDate = date('Y-m-d', strtotime($schedule['DepartureTime']));
              $today = date('Y-m-d');
              $isPast = $departureDate < $today;
              $isToday = $departureDate === $today;
              $isFuture = $departureDate > $today;
              
              $rowClass = '';
              $statusBadge = '';
              if ($isPast) {
                $rowClass = 'past-schedule';
                $statusBadge = '<span class="badge bg-secondary schedule-badge">Past</span>';
              } elseif ($isToday) {
                $rowClass = 'today-schedule';
                $statusBadge = '<span class="badge bg-warning schedule-badge">Today</span>';
              } else {
                $rowClass = 'future-schedule';
                $statusBadge = '<span class="badge bg-primary schedule-badge">Upcoming</span>';
              }
            ?>
            <tr class="<?= $rowClass ?>">
              <td>
                <strong><?= htmlspecialchars($schedule['BusNumber']) ?></strong>
                <br><small class="text-muted">Capacity: <?= $schedule['Capacity'] ?></small>
              </td>
              <td>
                <strong><?= htmlspecialchars($schedule['Origin']) ?></strong>
                <br><i class="fas fa-arrow-right text-muted"></i>
                <br><strong><?= htmlspecialchars($schedule['Destination']) ?></strong>
              </td>
              <td>
                <strong><?= date('M d, Y', strtotime($schedule['DepartureTime'])) ?></strong>
                <br><small class="text-muted"><?= date('l', strtotime($schedule['DepartureTime'])) ?></small>
              </td>
              <td>
                <i class="fas fa-clock text-success"></i>
                <?= date('h:i A', strtotime($schedule['DepartureTime'])) ?>
              </td>
              <td>
                <i class="fas fa-flag-checkered text-danger"></i>
                <?= date('h:i A', strtotime($schedule['ArrivalTime'])) ?>
              </td>
              <td>
                <strong>LKR <?= number_format($schedule['Fare'], 2) ?></strong>
              </td>
              <td><?= $statusBadge ?></td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary btn-sm" 
                          onclick="editSchedule(<?= $schedule['ID'] ?>)" 
                          title="Edit Schedule">
                    <i class="fas fa-edit"></i>
                  </button>
                  <?php if (!$isPast): ?>
                  <button type="button" class="btn btn-outline-danger btn-sm" 
                          onclick="deleteSchedule(<?= $schedule['ID'] ?>, '<?= htmlspecialchars($schedule['BusNumber']) ?>', '<?= $departureDate ?>')"
                          title="Delete Schedule">
                    <i class="fas fa-trash"></i>
                  </button>
                  <?php endif; ?>
                  <button type="button" class="btn btn-outline-info btn-sm" 
                          onclick="viewBookings(<?= $schedule['ID'] ?>)"
                          title="View Bookings">
                    <i class="fas fa-users"></i>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Average Fare Information -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Average Fare:</strong> LKR <?= number_format($statistics['avg_fare'], 2) ?>
        <span class="ms-3">
          <strong>Constraint:</strong> Each bus can only have one schedule per day.
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-plus-circle"></i> Add New Schedule
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="insert_schedule.php" id="addScheduleForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="bus_id" class="form-label">
                  <i class="fas fa-bus"></i> Select Bus *
                </label>
                <select name="bus_id" id="bus_id" class="form-select" required>
                  <option value="">Choose a bus...</option>
                  <?php foreach ($buses as $bus): ?>
                  <option value="<?= $bus['ID'] ?>" data-capacity="<?= $bus['Capacity'] ?>">
                    <?= htmlspecialchars($bus['BusNumber']) ?> - 
                    <?= htmlspecialchars($bus['Origin']) ?> to <?= htmlspecialchars($bus['Destination']) ?>
                    (Capacity: <?= $bus['Capacity'] ?>)
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="fare" class="form-label">
                  <i class="fas fa-money-bill"></i> Fare (LKR) *
                </label>
                <input type="number" name="fare" id="fare" class="form-control" 
                       step="0.01" min="0" required placeholder="0.00">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="departure_date" class="form-label">
                  <i class="fas fa-calendar"></i> Departure Date *
                </label>
                <input type="date" name="departure_date" id="departure_date" class="form-control" 
                       required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="departure_time" class="form-label">
                  <i class="fas fa-clock"></i> Departure Time *
                </label>
                <input type="time" name="departure_time" id="departure_time" class="form-control" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="arrival_date" class="form-label">
                  <i class="fas fa-calendar"></i> Arrival Date *
                </label>
                <input type="date" name="arrival_date" id="arrival_date" class="form-control" 
                       required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="arrival_time" class="form-label">
                  <i class="fas fa-flag-checkered"></i> Arrival Time *
                </label>
                <input type="time" name="arrival_time" id="arrival_time" class="form-control" required>
              </div>
            </div>
          </div>
          
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Important:</strong> Each bus can only have one schedule per day. Make sure the selected bus doesn't already have a schedule on the chosen date.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-maroon">
            <i class="fas fa-save"></i> Create Schedule
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-edit"></i> Edit Schedule
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="update_schedule.php" id="editScheduleForm">
        <input type="hidden" name="schedule_id" id="edit_schedule_id">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_bus_id" class="form-label">
                  <i class="fas fa-bus"></i> Select Bus *
                </label>
                <select name="bus_id" id="edit_bus_id" class="form-select" required>
                  <option value="">Choose a bus...</option>
                  <?php foreach ($buses as $bus): ?>
                  <option value="<?= $bus['ID'] ?>">
                    <?= htmlspecialchars($bus['BusNumber']) ?> - 
                    <?= htmlspecialchars($bus['Origin']) ?> to <?= htmlspecialchars($bus['Destination']) ?>
                    (Capacity: <?= $bus['Capacity'] ?>)
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_fare" class="form-label">
                  <i class="fas fa-money-bill"></i> Fare (LKR) *
                </label>
                <input type="number" name="fare" id="edit_fare" class="form-control" 
                       step="0.01" min="0" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_departure_date" class="form-label">
                  <i class="fas fa-calendar"></i> Departure Date *
                </label>
                <input type="date" name="departure_date" id="edit_departure_date" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_departure_time" class="form-label">
                  <i class="fas fa-clock"></i> Departure Time *
                </label>
                <input type="time" name="departure_time" id="edit_departure_time" class="form-control" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_arrival_date" class="form-label">
                  <i class="fas fa-calendar"></i> Arrival Date *
                </label>
                <input type="date" name="arrival_date" id="edit_arrival_date" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_arrival_time" class="form-label">
                  <i class="fas fa-flag-checkered"></i> Arrival Time *
                </label>
                <input type="time" name="arrival_time" id="edit_arrival_time" class="form-control" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Schedule
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bundle.min.js"></script>

<script>
// Auto-set arrival date when departure date changes
document.getElementById('departure_date').addEventListener('change', function() {
    const departureDate = this.value;
    document.getElementById('arrival_date').value = departureDate;
    document.getElementById('arrival_date').min = departureDate;
});

document.getElementById('edit_departure_date').addEventListener('change', function() {
    const departureDate = this.value;
    document.getElementById('edit_arrival_date').value = departureDate;
    document.getElementById('edit_arrival_date').min = departureDate;
});

// Edit schedule function
function editSchedule(scheduleId) {
    fetch(`get_schedule.php?id=${scheduleId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const schedule = data.schedule;
                document.getElementById('edit_schedule_id').value = schedule.ID;
                document.getElementById('edit_bus_id').value = schedule.BusID;
                document.getElementById('edit_fare').value = schedule.Fare;
                document.getElementById('edit_departure_date').value = schedule.ScheduleDate;
                document.getElementById('edit_departure_time').value = schedule.DepartureTimeOnly;
                document.getElementById('edit_arrival_date').value = schedule.ScheduleDate;
                document.getElementById('edit_arrival_time').value = schedule.ArrivalTimeOnly;
                
                new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
            } else {
                alert('Error loading schedule details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading schedule details.');
        });
}

// Delete schedule function
function deleteSchedule(scheduleId, busNumber, date) {
    if (confirm(`Are you sure you want to delete the schedule for bus ${busNumber} on ${date}?`)) {
        fetch(`delete_schedule.php?id=${scheduleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the schedule.');
            });
    }
}

// View bookings function
function viewBookings(scheduleId) {
    window.open(`schedule_bookings.php?schedule_id=${scheduleId}`, '_blank');
}

// Form validation
document.getElementById('addScheduleForm').addEventListener('submit', function(e) {
    const departureDate = document.getElementById('departure_date').value;
    const departureTime = document.getElementById('departure_time').value;
    const arrivalDate = document.getElementById('arrival_date').value;
    const arrivalTime = document.getElementById('arrival_time').value;
    
    const departureDateTime = new Date(departureDate + ' ' + departureTime);
    const arrivalDateTime = new Date(arrivalDate + ' ' + arrivalTime);
    
    if (departureDateTime >= arrivalDateTime) {
        e.preventDefault();
        alert('Arrival time must be after departure time.');
        return false;
    }
});

document.getElementById('editScheduleForm').addEventListener('submit', function(e) {
    const departureDate = document.getElementById('edit_departure_date').value;
    const departureTime = document.getElementById('edit_departure_time').value;
    const arrivalDate = document.getElementById('edit_arrival_date').value;
    const arrivalTime = document.getElementById('edit_arrival_time').value;
    
    const departureDateTime = new Date(departureDate + ' ' + departureTime);
    const arrivalDateTime = new Date(arrivalDate + ' ' + arrivalTime);
    
    if (departureDateTime >= arrivalDateTime) {
        e.preventDefault();
        alert('Arrival time must be after departure time.');
        return false;
    }
});
</script>

</body>
</html>