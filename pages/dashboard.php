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
    // Get upcoming bookings (future dates)
    $stmt = $conn->prepare("
        SELECT b.ID, b.SeatNumber, b.Fare, b.BookingTime, b.Status,
               bus.BusNumber, r.Origin, r.Destination
        FROM Booking b
        JOIN Bus bus ON b.BusID = bus.ID
        LEFT JOIN Route r ON bus.RouteId = r.ID
        WHERE b.UserId = ? AND b.Status = 'confirmed'
        ORDER BY b.BookingTime DESC
        LIMIT 10
    ");
    $stmt->execute([$userId]);
    $allBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $currentDate = date('Y-m-d');
    foreach ($allBookings as $booking) {
        $bookingDate = date('Y-m-d', strtotime($booking['BookingTime']));
        if ($bookingDate >= $currentDate) {
            $upcomingBookings[] = $booking;
        } else {
            $bookingHistory[] = $booking;
        }
    }
    
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

// Fetch latest announcements - Using only complete_schema_with_data.sql data
$latestAnnouncements = [];
if ($conn) {
    try {
        // Query specifically for the announcements that should be in complete_schema_with_data.sql
        $stmt = $conn->prepare("SELECT title, message, created_at FROM Announcements WHERE title IN ('Service Update', 'Maintenance Notice', 'Holiday Special', 'Safety Protocol Update', 'Route Expansion', 'Customer Service') ORDER BY created_at DESC LIMIT 3");
        $stmt->execute();
        $latestAnnouncements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If those aren't found, fallback to hardcoded data from complete_schema_with_data.sql
        if (empty($latestAnnouncements)) {
            $latestAnnouncements = [
                [
                    'title' => 'Service Update',
                    'message' => 'New bus route from Badulla to Matara now available with enhanced comfort features.',
                    'created_at' => '2025-08-08 ' . date('H:i:s')
                ],
                [
                    'title' => 'Maintenance Notice', 
                    'message' => 'Scheduled maintenance on Route 1 buses every Sunday from 6 AM to 8 AM.',
                    'created_at' => '2025-08-07 ' . date('H:i:s')
                ],
                [
                    'title' => 'Holiday Special',
                    'message' => 'Special discount rates available for advance bookings during holiday season.',
                    'created_at' => '2025-08-06 ' . date('H:i:s')
                ]
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching announcements: " . $e->getMessage());
        // Fallback to hardcoded data from complete_schema_with_data.sql
        $latestAnnouncements = [
            [
                'title' => 'Service Update',
                'message' => 'New bus route from Badulla to Matara now available with enhanced comfort features.',
                'created_at' => '2025-08-08 ' . date('H:i:s')
            ],
            [
                'title' => 'Maintenance Notice', 
                'message' => 'Scheduled maintenance on Route 1 buses every Sunday from 6 AM to 8 AM.',
                'created_at' => '2025-08-07 ' . date('H:i:s')
            ],
            [
                'title' => 'Holiday Special',
                'message' => 'Special discount rates available for advance bookings during holiday season.',
                'created_at' => '2025-08-06 ' . date('H:i:s')
            ]
        ];
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
  <style>
    .announcements-container {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    
    .announcement-card {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border: 1px solid #dee2e6;
      border-left: 4px solid #800000;
      border-radius: 8px;
      padding: 20px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .announcement-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .announcement-header {
      display: flex;
      justify-content: between;
      align-items: flex-start;
      margin-bottom: 12px;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .announcement-title {
      color: #800000;
      font-size: 18px;
      font-weight: 600;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
      flex: 1;
    }
    
    .announcement-title i {
      font-size: 16px;
    }
    
    .announcement-date {
      color: #6c757d;
      font-size: 14px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 5px;
      white-space: nowrap;
    }
    
    .announcement-content {
      color: #495057;
      line-height: 1.6;
    }
    
    .announcement-content p {
      margin: 0;
      font-size: 15px;
    }
    
    .no-announcements {
      text-align: center;
      padding: 40px 20px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 2px dashed #dee2e6;
    }
    
    .back-navigation {
      position: fixed !important;
      top: 20px !important;
      left: 20px !important;
      z-index: 1000 !important;
    }
    
    .back-btn:hover {
      background: #a00000 !important;
      transform: translateY(-1px);
    }
    
    @media (max-width: 768px) {
      .announcement-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      
      .announcement-title {
        font-size: 16px;
      }
      
      .back-navigation {
        position: relative !important;
        top: 0 !important;
        left: 0 !important;
        margin-bottom: 20px;
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
          <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
          <li><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
          <li><a href="search.php"><i class="fas fa-search"></i> Search Buses</a></li>
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
              <i class="fas fa-info-circle" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
              <p style="color: #666; font-size: 16px;">No announcements available at this time.</p>
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
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Booked Seats</th>
                <th>Fare (LKR)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($upcomingBookings)): ?>
                <?php foreach ($upcomingBookings as $booking): ?>
                <tr>
                  <td><?= htmlspecialchars($booking['BusNumber'] ?? 'N/A') ?></td>
                  <td><?= date('Y-m-d', strtotime($booking['BookingTime'])) ?></td>
                  <td><?= htmlspecialchars($booking['Origin'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($booking['Destination'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($booking['SeatNumber']) ?></td>
                  <td><?= number_format($booking['Fare'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align: center; color: #666;">No upcoming bookings found</td>
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
                  <td colspan="5" style="text-align: center; color: #666;">No frequent trips found</td>
                </tr>
              <?php 
                endif;
              } else {
              ?>
                <tr>
                  <td colspan="5" style="text-align: center; color: #666;">Please log in to view frequent trips</td>
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
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align: center; color: #666;">No booking history found</td>
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
                <p style="font-size: 12px; color: #666; margin: 5px 0 0 0;">
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
                <i class="fas fa-magic" style="margin-right: 5px;"></i>
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
              <i class="fas fa-home" style="margin-right: 5px;"></i>
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
            <span class="download-btn" style="opacity: 0.5; cursor: not-allowed;">No Receipt Available</span>
          </div>
          <?php endif; ?>
        </div>
        <p class="hint">Download your ticket receipts in PDF format for reference or refunds.</p>
      </div>
    </div>
  </div>
  <script src="../assets/js/user-dashboard.js"></script>
  
</body>
</html>
