<?php
// Start session first
session_start();

// DEBUG: Check what's in the session (remove this after testing)
error_log("DEBUG SESSION: " . print_r($_SESSION, true));

// Immediate authentication check - BLOCK ALL UNAUTHENTICATED ACCESS
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    // DEBUG: Log the failed authentication attempt
    error_log("SECURITY: Unauthorized access attempt to dashboard.php");
    
    session_unset();
    session_destroy();
    
    // Multiple redirect methods to ensure it works
    if (!headers_sent()) {
        header('Location: ../auth/login.php?error=access_denied', true, 302);
    }
    
    // Fallback JavaScript redirect
    echo '<script>window.location.href="../auth/login.php?error=access_denied";</script>';
    
    // Fallback HTML redirect
    echo '<meta http-equiv="refresh" content="0;url=../auth/login.php?error=access_denied">';
    
    // Stop all execution
    die('Access Denied. Redirecting to login...');
}

require_once '../classes/Database.php';

// Get user data from session
$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

// CRITICAL: Verify user exists in database - MANDATORY CHECK
if (!$conn) {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php?error=database_error', true, 302);
    exit();
}

$stmt = $conn->prepare("SELECT Name FROM User WHERE ID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // User doesn't exist in database - SECURITY BREACH ATTEMPT
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php?error=invalid_session', true, 302);
    exit();
}

// Update username from database (most current)
$username = htmlspecialchars($user['Name']);

// Additional security: Regenerate session ID to prevent session hijacking
session_regenerate_id(true);

// Set session timeout (optional but recommended)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    // Session expired after 1 hour of inactivity
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php?error=session_expired', true, 302);
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Get database connection (reuse if already created)
if (!isset($database)) {
    $database = new Database();
    $conn = $database->getConnection();
}

// Fetch user's bookings
$upcomingBookings = [];
$bookingHistory = [];
$recentBooking = null;

if ($userId && $conn) {
    // Get user's bookings - BookingTime appears to be used as travel date in this database
    $stmt = $conn->prepare("
        SELECT b.ID, b.SeatNumber, b.Fare, b.BookingTime, b.Status,
               bus.BusNumber, r.Origin, r.Destination, bus.ID as BusID,
               f.ID as FeedbackID, f.Rating, f.Comment
        FROM Booking b
        JOIN Bus bus ON b.BusID = bus.ID
        LEFT JOIN Route r ON bus.RouteId = r.ID
        LEFT JOIN Feedback f ON bus.ID = f.BusId AND f.UserId = ? 
                              AND DATE(f.CreatedDate) = DATE(b.BookingTime)
        WHERE b.UserId = ? AND b.Status = 'confirmed'
        ORDER BY b.BookingTime ASC
    ");
    $stmt->execute([$userId, $userId]);
    $allBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Log what we found
    error_log("DEBUG: Found " . count($allBookings) . " bookings for user " . $userId);
    if (!empty($allBookings)) {
        error_log("DEBUG: First booking: " . print_r($allBookings[0], true));
    }
    
    $currentDate = date('Y-m-d');
    $currentDateTime = date('Y-m-d H:i:s');
    
    foreach ($allBookings as $booking) {
        // Compare full datetime for more accurate results
        $bookingDateTime = $booking['BookingTime'];
        
        // For upcoming: booking datetime should be in the future
        if ($bookingDateTime > $currentDateTime) {
            $upcomingBookings[] = $booking;
        } else {
            $bookingHistory[] = $booking;
        }
    }
    
    // Debug: Log results
    error_log("DEBUG: Upcoming bookings: " . count($upcomingBookings));
    error_log("DEBUG: Historical bookings: " . count($bookingHistory));
    error_log("DEBUG: Current datetime: " . $currentDateTime);
    
    // Get most recent booking for rebooking section
    if (!empty($allBookings)) {
        $recentBooking = $allBookings[0];
    }
    
    // Smart rebooking algorithm - analyze user's booking patterns
    $userPreferences = [];
    if (count($allBookings) >= 2) { // User has multiple bookings - returning customer
        // Calculate user's favorite routes and preferences
        $routeFrequency = [];
        $totalFares = [];
        
        foreach ($allBookings as $booking) {
            $route = $booking['Origin'] . ' → ' . $booking['Destination'];
            $routeFrequency[$route] = ($routeFrequency[$route] ?? 0) + 1;
            $totalFares[] = floatval($booking['Fare']);
        }
        
        // Find most frequent route
        $mostFrequentRoute = array_keys($routeFrequency, max($routeFrequency))[0];
        list($favoriteFrom, $favoriteTo) = explode(' → ', $mostFrequentRoute);
        
        // Calculate average and max price user typically pays
        $avgPrice = round(array_sum($totalFares) / count($totalFares), 2);
        $maxPrice = max($totalFares);
        
        $userPreferences = [
            'from' => $favoriteFrom,
            'to' => $favoriteTo,
            'avgPrice' => $avgPrice,
            'maxPrice' => $maxPrice,
            'totalBookings' => count($allBookings),
            'isReturningCustomer' => true
        ];
    } else {
        // First time user or single booking
        $userPreferences = [
            'isReturningCustomer' => false,
            'totalBookings' => count($allBookings)
        ];
    }
}

// Fetch latest announcements - Get the most recent announcements from database
$latestAnnouncements = [];
if ($conn) {
    try {
        // Get the 3 most recent announcements ordered by creation date (latest first)
        $stmt = $conn->prepare("SELECT title, message, created_at FROM Announcements ORDER BY created_at DESC LIMIT 3");
        $stmt->execute();
        $latestAnnouncements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching announcements: " . $e->getMessage());
        $latestAnnouncements = [];
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LankaTransit Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../assets/css/user-dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
      <a href="dashboard.php" class="active">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
      <a href="incidents.php">
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
    <hr class="sidebar-hr">

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
          <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
          <li><a href="cancel_booking.php"><i class="fas fa-times-circle"></i> Cancel Booking</a></li>
        </ul>
      </nav>
      <div class="logout">
        <a href="../auth/Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
    <div class="main-content">
      <div class="header-container">
        <div class="welcome-message">
          <span class="welcome-icon">👋</span>
          <h1>Hello, <?= $username ?>!</h1>
        </div>
      </div>

      <!-- Latest Announcements -->
      <div class="section">
        <h2><i class="fas fa-bullhorn section-icon"></i> Latest Announcements</h2>
        <div class="announcements-container">
          <?php if (!empty($latestAnnouncements)): ?>
            <?php foreach ($latestAnnouncements as $announcement): ?>
              <div class="announcement-card">
                <div class="announcement-header">
                  <h4 class="announcement-title">
                    <i class="fas fa-info-circle"></i>
                    <?= htmlspecialchars($announcement['title']) ?>
                  </h4>
                  <span class="announcement-date">
                    <i class="fas fa-clock"></i>
                    <?php 
                      echo date('M j, Y', strtotime($announcement['created_at'])); 
                    ?>
                  </span>
                </div>
                <div class="announcement-content">
                  <p><?= htmlspecialchars($announcement['message']) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-announcements">
              <i class="fas fa-info-circle no-announcements-icon"></i>
              <p class="no-announcements-text">No announcements available at this time.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Upcoming Bookings -->
      <div class="section">
        <h2><i class="fas fa-calendar-alt section-icon"></i> Upcoming Bookings</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Bus No.</th>
                <th>Travel Date & Time</th>
                <th>From</th>
                <th>To</th>
                <th>Seat</th>
                <th>Fare (LKR)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($upcomingBookings)): ?>
                <?php foreach ($upcomingBookings as $booking): ?>
                <tr>
                  <td><?= htmlspecialchars($booking['BusNumber'] ?? 'N/A') ?></td>
                  <td><?= date('M j, Y \a\t g:i A', strtotime($booking['BookingTime'])) ?></td>
                  <td><?= htmlspecialchars($booking['Origin'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($booking['Destination'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($booking['SeatNumber']) ?></td>
                  <td><?= number_format($booking['Fare'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="table-empty-message">No upcoming bookings found</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Schedule a Frequent Trip -->
      <div class="section">
        <h2><i class="fas fa-route section-icon"></i> Schedule a Frequent Trip</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Origin</th>
                <th>Destination</th>
                <th>Last Used</th>
                <th>Times Booked</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if ($userId && $conn) {
                // Get frequent routes
                $stmt = $conn->prepare("
                  SELECT r.Origin, r.Destination, MAX(b.BookingTime) as LastUsed, COUNT(*) as TimesBooked
                  FROM Booking b
                  JOIN Bus bus ON b.BusID = bus.ID
                  LEFT JOIN Route r ON bus.RouteId = r.ID
                  WHERE b.UserId = ? AND r.Origin IS NOT NULL
                  GROUP BY r.Origin, r.Destination
                  ORDER BY COUNT(*) DESC, MAX(b.BookingTime) DESC
                  LIMIT 3
                ");
                $stmt->execute([$userId]);
                $frequentRoutes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($frequentRoutes)):
                  foreach ($frequentRoutes as $route):
              ?>
                <tr>
                  <td><?= htmlspecialchars($route['Origin']) ?></td>
                  <td><?= htmlspecialchars($route['Destination']) ?></td>
                  <td><?= date('Y-m-d', strtotime($route['LastUsed'])) ?></td>
                  <td><?= $route['TimesBooked'] ?></td>
                  <td>
                    <form action="search.php" method="get">
                      <input type="hidden" name="origin" value="<?= htmlspecialchars($route['Origin']) ?>">
                      <input type="hidden" name="destination" value="<?= htmlspecialchars($route['Destination']) ?>">
                      <input type="hidden" name="travel_date" value="<?= date('Y-m-d') ?>">
                      <button class="rebook-btn">Book Again</button>
                    </form>
                  </td>
                </tr>
              <?php 
                  endforeach;
                else:
              ?>
                <tr>
                  <td colspan="5" class="table-empty-message">No frequent trips found</td>
                </tr>
              <?php 
                endif;
              } else {
              ?>
                <tr>
                  <td colspan="5" class="table-empty-message">Please log in to view frequent trips</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="section">
        <h2><i class="fas fa-book-reader section-icon"></i> My Booking History</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Bus No.</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Booked Seats</th>
                <th>Fare (LKR)</th>
                <th>Feedback</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($bookingHistory)): ?>
                <?php foreach (array_slice($bookingHistory, 0, 5) as $booking): ?>
                <tr>
                  <td><?= htmlspecialchars($booking['BusNumber'] ?? 'N/A') ?></td>
                  <td><?= date('Y-m-d', strtotime($booking['BookingTime'])) ?></td>
                  <td><?= htmlspecialchars($booking['Origin'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($booking['Destination'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($booking['SeatNumber']) ?></td>
                  <td><?= number_format($booking['Fare'], 2) ?></td>
                  <td>
                    <?php if (!empty($booking['FeedbackID'])): ?>
                      <div class="feedback-status">
                        <span class="rating-stars">
                          <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $booking['Rating'] ? 'fas' : 'far' ?> fa-star"></i>
                          <?php endfor; ?>
                        </span>
                        <small>Rated</small>
                      </div>
                    <?php else: ?>
                      <button class="feedback-btn" onclick="openFeedbackModal(<?= $booking['ID'] ?>, '<?= htmlspecialchars($booking['BusNumber']) ?>', <?= $booking['BusID'] ?>)">
                        <i class="fas fa-star"></i> Rate Trip
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="table-empty-message">No booking history found</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="section">
        <h2><i class="fas fa-redo section-icon"></i>Rebooking</h2>
        <div class="rebooking-card-wrapper">
          <?php if ($userPreferences['isReturningCustomer']): ?>
          <div class="rebooking-card">
            <div class="rebooking-details">
              <i class="far fa-calendar-alt rebooking-icon"></i>
              <div>
                <p class="route"><?= htmlspecialchars($userPreferences['from']) ?> &rarr; <?= htmlspecialchars($userPreferences['to']) ?></p>
                <p class="rebooking-hint">
                  Suggestion based on your <?= $userPreferences['totalBookings'] ?> previous bookings
                </p>
              </div>
            </div>
            <form action="search.php" method="get">
              <input type="hidden" name="from_city" value="<?= htmlspecialchars($userPreferences['from']) ?>">
              <input type="hidden" name="to_city" value="<?= htmlspecialchars($userPreferences['to']) ?>">
              <input type="hidden" name="travel_date" value="<?= date('Y-m-d') ?>">
              <input type="hidden" name="max_price" value="<?= $userPreferences['maxPrice'] ?>">
              <input type="hidden" name="smart_search" value="1">
              <button class="rebook-btn">
                <i class="fas fa-magic icon-with-margin"></i>
                Search
              </button>
            </form>
          </div>
          <?php else: ?>
          <div class="rebooking-card">
            <div class="rebooking-details">
              <i class="fas fa-star rebooking-icon"></i>
              <div>
                <?php if ($userPreferences['totalBookings'] == 0): ?>
                <p class="route">Welcome to Lanka Transit!</p>
                <p class="last-traveled">Start your journey with us - explore our routes and find your perfect trip</p>
                <?php else: ?>
                <p class="route">Building your preferences...</p>
                <p class="last-traveled">Make a few more bookings to unlock recommendations</p>
                <?php endif; ?>
              </div>
            </div>
            <a href="../index.php" class="rebook-btn">
              <i class="fas fa-home icon-with-margin"></i>
              Explore Routes
            </a>
          </div>
          <?php endif; ?>
        </div>
        
      </div>

      <div class="section">
        <h2><i class="fas fa-receipt section-icon"></i> Receipts</h2>
        <div class="receipt-card-wrapper">
          <?php if ($recentBooking): ?>
          <div class="receipt-card">
            <div class="receipt-details">
              <i class="far fa-file-alt receipt-icon"></i>
              <div>
                <p class="receipt-booking-id">Bus #<?= htmlspecialchars($recentBooking['BusNumber'] ?? 'N/A') ?></p>
                <p class="receipt-date">Date: <?= date('d F Y', strtotime($recentBooking['BookingTime'])) ?> • <?= htmlspecialchars($recentBooking['Origin'] ?? 'N/A') ?> &rarr; <?= htmlspecialchars($recentBooking['Destination'] ?? 'N/A') ?></p>
              </div>
            </div>
            <a href="ticket_pdf.php?ref=LT-<?= str_pad($recentBooking['ID'], 6, '0', STR_PAD_LEFT) ?>&name=<?= urlencode($username) ?>&phone=&origin=<?= urlencode($recentBooking['Origin'] ?? '') ?>&destination=<?= urlencode($recentBooking['Destination'] ?? '') ?>&date=<?= urlencode(date('Y-m-d', strtotime($recentBooking['BookingTime']))) ?>&bus=<?= urlencode($recentBooking['BusNumber'] ?? '') ?>&seat=<?= urlencode($recentBooking['SeatNumber']) ?>&fare=<?= urlencode($recentBooking['Fare']) ?>" 
               class="download-btn">Download PDF</a>
          </div>
          <?php else: ?>
          <div class="receipt-card">
            <div class="receipt-details">
              <i class="far fa-file-alt receipt-icon"></i>
              <div>
                <p class="receipt-booking-id">No recent bookings</p>
                <p class="receipt-date">Make a booking to download receipts</p>
              </div>
            </div>
            <span class="download-btn download-btn-disabled">No Receipt Available</span>
          </div>
          <?php endif; ?>
        </div>
        <p class="hint">Download your ticket receipts in PDF format for reference or refunds.</p>
      </div>
    </div>
  </div>

  <!-- Feedback Modal -->
  <div id="feedbackModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Rate Your Trip</h3>
        <span class="close" onclick="closeFeedbackModal()">&times;</span>
      </div>
      <div class="modal-body">
        <div class="trip-info">
          <p><strong>Bus:</strong> <span id="modalBusNumber"></span></p>
        </div>
        <form id="feedbackForm">
          <input type="hidden" id="bookingId" name="bookingId">
          <input type="hidden" id="busId" name="busId">
          
          <div class="rating-section">
            <label>Rate your experience:</label>
            <div class="star-rating">
              <input type="radio" name="rating" value="5" id="star5">
              <label for="star5" class="star">★</label>
              <input type="radio" name="rating" value="4" id="star4">
              <label for="star4" class="star">★</label>
              <input type="radio" name="rating" value="3" id="star3">
              <label for="star3" class="star">★</label>
              <input type="radio" name="rating" value="2" id="star2">
              <label for="star2" class="star">★</label>
              <input type="radio" name="rating" value="1" id="star1">
              <label for="star1" class="star">★</label>
            </div>
          </div>
          
          <div class="comment-section">
            <label for="comment">Comments (Optional):</label>
            <textarea id="comment" name="comment" rows="4" placeholder="Share your experience..."></textarea>
          </div>
          
          <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeFeedbackModal()">Cancel</button>
            <button type="submit" class="btn-submit">Submit Feedback</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
    
    // Close modal when clicking outside
    if (event.target == document.getElementById('feedbackModal')) {
        closeFeedbackModal();
    }
}

// Feedback Modal Functions
function openFeedbackModal(bookingId, busNumber, busId) {
    document.getElementById('bookingId').value = bookingId;
    document.getElementById('busId').value = busId;
    document.getElementById('modalBusNumber').textContent = busNumber;
    document.getElementById('feedbackModal').style.display = 'block';
    
    // Reset form
    document.getElementById('feedbackForm').reset();
}

function closeFeedbackModal() {
    document.getElementById('feedbackModal').style.display = 'none';
}

// Handle feedback form submission
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const rating = document.querySelector('input[name="rating"]:checked');
    
    if (!rating) {
        alert('Please select a rating');
        return;
    }
    
    // Send feedback to backend
    fetch('../pages/submit_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you for your feedback!');
            closeFeedbackModal();
            location.reload(); // Refresh to show updated feedback status
        } else {
            alert('Error submitting feedback: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting feedback. Please try again.');
    });
});
</script>
  
</body>
</html>
