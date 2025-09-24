<?php
// Start session first
require_once __DIR__ . '/../includes/session_config.php';

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

$stmt = $conn->prepare("SELECT Name, PhoneNumber FROM User WHERE ID = ?");
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
$userPhoneNumber = $user['PhoneNumber'] ?? null;

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

// Fetch booking history based on user's phone number
$upcomingBookings = [];
$bookingHistory = [];
$recentBooking = null;
$userPreferences = ['isReturningCustomer' => false, 'totalBookings' => 0];

if ($userPhoneNumber && $conn) {
    try {
        // Get user's bookings using phone number from Booking table
        // Join with Bus and Route tables to get complete booking details
        $stmt = $conn->prepare("
            SELECT b.ID, b.SeatNumber, b.Fare, b.BookingTime, b.Status,
                   bus.BusNumber, r.Origin, r.Destination, bus.ID as BusID,
                   f.ID as FeedbackID, f.Rating, f.Comment
            FROM Booking b
            JOIN Bus bus ON b.BusID = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Feedback f ON f.BusId = bus.ID 
                                  AND f.UserId = ? 
                                  AND DATE(f.CreatedDate) = DATE(b.BookingTime)
            WHERE b.PhoneNumber = ? AND b.Status IN ('confirmed', 'completed')
            ORDER BY b.BookingTime ASC
        ");
        $stmt->execute([$userId, $userPhoneNumber]);
        $allBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Separate bookings into upcoming and history based on booking time
        $currentDateTime = date('Y-m-d H:i:s');
        
        foreach ($allBookings as $booking) {
            $bookingDateTime = $booking['BookingTime'];
            
            // Compare booking time with current time to determine if it's upcoming or past
            if ($bookingDateTime > $currentDateTime) {
                // Future bookings go to upcoming
                $upcomingBookings[] = $booking;
            } else {
                // Past bookings go to history
                $bookingHistory[] = $booking;
            }
        }
        
        // Sort upcoming bookings by time (earliest first)
        usort($upcomingBookings, function($a, $b) {
            return strtotime($a['BookingTime']) - strtotime($b['BookingTime']);
        });
        
        // Sort booking history by time (latest first)
        usort($bookingHistory, function($a, $b) {
            return strtotime($b['BookingTime']) - strtotime($a['BookingTime']);
        });
        
        // Set recent booking for other sections if needed
        if (!empty($allBookings)) {
            $recentBooking = $allBookings[0];
        }
        
        // Log for debugging
        error_log("DEBUG: Found " . count($allBookings) . " total bookings for phone " . $userPhoneNumber);
        error_log("DEBUG: " . count($upcomingBookings) . " upcoming bookings, " . count($bookingHistory) . " past bookings");
        
    } catch (PDOException $e) {
        error_log("Error fetching bookings: " . $e->getMessage());
        $bookingHistory = [];
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
        <h2><i class="fas fa-redo section-icon"></i> Rebook a Frequent Trip</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Origin</th>
                <th>Destination</th>
                <th>Last Used</th>
                <th>Times Booked</th>
                <th>Avg Fare (LKR)</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // Get top 5 frequent routes based on user's phone number, ordered by booking count
              $frequentRoutes = [];
              if ($userPhoneNumber && $conn) {
                  try {
                      // Query to get top 5 most frequent routes for the user
                      $stmt = $conn->prepare("
                          SELECT r.Origin, r.Destination, 
                                 MAX(b.BookingTime) as LastUsed, 
                                 COUNT(*) as TimesBooked,
                                 AVG(b.Fare) as AvgFare
                          FROM Booking b
                          JOIN Bus bus ON b.BusID = bus.ID
                          LEFT JOIN Route r ON bus.RouteId = r.ID
                          WHERE b.PhoneNumber = ? 
                            AND b.Status IN ('confirmed', 'completed')
                            AND r.Origin IS NOT NULL 
                            AND r.Destination IS NOT NULL
                          GROUP BY r.Origin, r.Destination
                          ORDER BY COUNT(*) DESC, MAX(b.BookingTime) DESC
                          LIMIT 5
                      ");
                      $stmt->execute([$userPhoneNumber]);
                      $frequentRoutes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      
                      // Log for debugging
                      error_log("DEBUG: Found " . count($frequentRoutes) . " frequent routes for rebooking, phone " . $userPhoneNumber);
                      
                  } catch (PDOException $e) {
                      error_log("Error fetching frequent routes for rebooking: " . $e->getMessage());
                      $frequentRoutes = [];
                  }
              }
              
              if (!empty($frequentRoutes)): ?>
                <?php foreach ($frequentRoutes as $index => $route): ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($route['Origin']) ?>
                    <?php if ($index === 0): ?>
                      <span class="badge" style="background: #4B0000; color: white; font-size: 0.7rem; margin-left: 5px;">MOST FREQUENT</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($route['Destination']) ?></td>
                  <td><?= date('M j, Y', strtotime($route['LastUsed'])) ?></td>
                  <td>
                    <strong style="color: #4B0000;"><?= $route['TimesBooked'] ?></strong>
                    <?= $route['TimesBooked'] == 1 ? 'trip' : 'trips' ?>
                  </td>
                  <td><?= number_format($route['AvgFare'], 2) ?></td>
                  <td>
                    <form action="search.php" method="post" style="margin: 0;">
                      <input type="hidden" name="origin" value="<?= htmlspecialchars($route['Origin']) ?>">
                      <input type="hidden" name="destination" value="<?= htmlspecialchars($route['Destination']) ?>">
                      <input type="hidden" name="travel_date" value="<?= date('Y-m-d') ?>">
                      <input type="hidden" name="max_fare" value="<?= ceil($route['AvgFare'] * 1.2) ?>">
                      <button type="submit" class="rebook-btn">
                        <?php if ($index === 0): ?>
                          <i class="fas fa-star icon-with-margin"></i>
                          Book Again
                        <?php else: ?>
                          <i class="fas fa-redo icon-with-margin"></i>
                          Rebook
                        <?php endif; ?>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="table-empty-message">
                    <div style="text-align: center; padding: 40px;">
                      <i class="fas fa-route" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                      <p style="margin: 0; color: #666;">No frequent trips found</p>
                      <small style="color: #888;">Make some bookings to see your frequent routes here!</small>
                      <div style="margin-top: 15px;">
                        <a href="../index.php" class="rebook-btn" style="display: inline-block; text-decoration: none;">
                          <i class="fas fa-search icon-with-margin"></i>
                          Search Routes
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <p class="hint">
          <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
          Your favorite routes, sorted by how often you travel. Click "Book Again" to find buses.
        </p>
      </div>

      <div class="section">
        <h2><i class="fas fa-receipt section-icon"></i> Receipts</h2>
        <div class="receipt-card-wrapper">
          <?php 
          // Get all bookings for receipts based on user's phone number
          $receiptBookings = [];
          if ($userPhoneNumber && $conn) {
              try {
                  // Get all confirmed/completed bookings for receipt generation
                  $stmt = $conn->prepare("
                      SELECT b.ID, b.SeatNumber, b.Fare, b.BookingTime, b.Status,
                             bus.BusNumber, r.Origin, r.Destination, bus.ID as BusID
                      FROM Booking b
                      JOIN Bus bus ON b.BusID = bus.ID
                      LEFT JOIN Route r ON bus.RouteId = r.ID
                      WHERE b.PhoneNumber = ? 
                        AND b.Status IN ('confirmed', 'completed')
                      ORDER BY b.BookingTime DESC
                      LIMIT 10
                  ");
                  $stmt->execute([$userPhoneNumber]);
                  $receiptBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  
                  // Log for debugging
                  error_log("DEBUG: Found " . count($receiptBookings) . " bookings for receipts, phone " . $userPhoneNumber);
                  
              } catch (PDOException $e) {
                  error_log("Error fetching receipt bookings: " . $e->getMessage());
                  $receiptBookings = [];
              }
          }
          ?>
          
          <?php if (!empty($receiptBookings)): ?>
            <div style="display: grid; gap: 15px; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
              <?php foreach ($receiptBookings as $booking): ?>
              <div class="receipt-card">
                <div class="receipt-details">
                  <i class="far fa-file-alt receipt-icon"></i>
                  <div>
                    <p class="receipt-booking-id">
                      <strong>Bus #<?= htmlspecialchars($booking['BusNumber'] ?? 'N/A') ?></strong>
                      <span style="font-size: 0.85em; color: #666; margin-left: 8px;">
                        <?= 'LT-' . str_pad($booking['ID'], 6, '0', STR_PAD_LEFT) ?>
                      </span>
                    </p>
                    <p class="receipt-date">
                      <i class="fas fa-calendar-check" style="margin-right: 5px; color: #4a90e2;"></i>
                      <?= date('d M Y', strtotime($booking['BookingTime'])) ?>
                      <span style="margin: 0 8px;">•</span>
                      <?= htmlspecialchars($booking['Origin'] ?? 'N/A') ?> → <?= htmlspecialchars($booking['Destination'] ?? 'N/A') ?>
                    </p>
                    <p style="font-size: 0.85em; color: #666; margin-top: 5px;">
                      <i class="fas fa-chair" style="margin-right: 5px;"></i>
                      Seat: <?= htmlspecialchars($booking['SeatNumber']) ?>
                      <span style="margin: 0 8px;">•</span>
                      <i class="fas fa-money-bill-wave" style="margin-right: 5px;"></i>
                      LKR <?= number_format($booking['Fare'], 2) ?>
                    </p>
                  </div>
                </div>
                <a href="ticket_pdf.php?ref=LT-<?= str_pad($booking['ID'], 6, '0', STR_PAD_LEFT) ?>&name=<?= urlencode($username) ?>&phone=<?= urlencode($userPhoneNumber) ?>&origin=<?= urlencode($booking['Origin'] ?? '') ?>&destination=<?= urlencode($booking['Destination'] ?? '') ?>&date=<?= urlencode(date('Y-m-d', strtotime($booking['BookingTime']))) ?>&bus=<?= urlencode($booking['BusNumber'] ?? '') ?>&seat=<?= urlencode($booking['SeatNumber']) ?>&fare=<?= urlencode($booking['Fare']) ?>" 
                   class="download-btn" target="_blank">
                  <i class="fas fa-download" style="margin-right: 5px;"></i>
                  Download PDF
                </a>
              </div>
              <?php endforeach; ?>
            </div>
            
            <?php if (count($receiptBookings) >= 10): ?>
            <div style="text-align: center; margin-top: 15px;">
              <small style="color: #666;">
                <i class="fas fa-info-circle"></i>
                Showing latest 10 bookings. Contact support for older receipts.
              </small>
            </div>
            <?php endif; ?>
            
          <?php else: ?>
          <!-- No bookings found -->
          <div class="receipt-card">
            <div class="receipt-details">
              <i class="far fa-file-alt receipt-icon"></i>
              <div>
                <p class="receipt-booking-id">No bookings found</p>
                <p class="receipt-date">Make a booking to download receipts</p>
              </div>
            </div>
            <span class="download-btn download-btn-disabled">No Receipt Available</span>
          </div>
          <?php endif; ?>
        </div>
        <p class="hint">
          <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
          Download PDF receipts for your bookings with all travel details included.
        </p>
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
        <!-- Notification area for feedback -->
        <div id="feedbackNotification" class="notification" style="display: none;">
          <span id="feedbackNotificationMessage"></span>
        </div>
        
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
    // Clear any notifications when closing
    hideNotification();
}

// Function to show notifications in the modal
function showNotification(message, type) {
    const notification = document.getElementById('feedbackNotification');
    const notificationMessage = document.getElementById('feedbackNotificationMessage');
    
    notificationMessage.textContent = message;
    notification.className = `notification ${type}`;
    notification.style.display = 'block';
    
    // Auto-hide after 5 seconds for success messages
    if (type === 'success') {
        setTimeout(() => {
            hideNotification();
        }, 5000);
    }
}

function hideNotification() {
    const notification = document.getElementById('feedbackNotification');
    notification.style.display = 'none';
}

// Handle feedback form submission
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const rating = document.querySelector('input[name="rating"]:checked');
    
    if (!rating) {
        showNotification('Please select a rating before submitting your feedback', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('.btn-submit');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    // Send feedback to backend
    fetch('../pages/submit_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Feedback response:', data); // Debug logging
        if (data.success) {
            showNotification(data.message || 'Thank you for your feedback!', 'success');
            // Reset form after successful submission
            this.reset();
            // Refresh the page after a short delay to show updated feedback status
            setTimeout(() => {
                closeFeedbackModal();
                location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Unable to submit feedback. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Unable to submit feedback at the moment. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});
</script>
  
</body>
</html>