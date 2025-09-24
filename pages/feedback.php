<?php
require_once __DIR__ . "/../includes/session_config.php";

// Include database configuration
require_once '../classes/Database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?error=login_required');
    exit();
}

$userId = $_SESSION['user_id'];

// Get username from database for sidebar
$stmt = $conn->prepare("SELECT Name FROM User WHERE ID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['Name']) : 'User';

// Fetch user's previous bookings with trip details (only past trips)
$stmt = $conn->prepare("
    SELECT 
        b.ID as BookingID,
        b.SeatNumber,
        b.BookingTime,
        b.Status,
        bus.BusNumber,
        r.Origin,
        r.Destination,
        CONCAT('LT-', LPAD(b.ID, 6, '0')) as BookingRef
    FROM Booking b
    JOIN Bus bus ON b.BusID = bus.ID
    LEFT JOIN Route r ON bus.RouteId = r.ID
    WHERE b.UserId = ? 
    AND b.Status IN ('confirmed', 'completed')
    AND b.BookingTime < NOW()
    ORDER BY b.BookingTime DESC
");
$stmt->execute([$userId]);
$userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Feedback Form - LankaTransit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/user-dashboard.css" />
  <link rel="stylesheet" href="../assets/css/user-feedback.css" />
  <style>
    .star-rating {
      text-align: left;
      padding: 15px 0;
    }
    
    .stars {
      font-size: 2rem;
      margin-bottom: 10px;
    }
    
    .star {
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s ease;
      margin: 0 2px;
    }
    
    .star:hover,
    .star.active {
      color: #ffc107;
    }
    
    .star.hovered {
      color: #ffc107;
    }
    
    .rating-text {
      font-size: 14px;
      color: #666;
      margin-top: 8px;
      font-weight: 500;
    }
    
    .rating-selected {
      color: #800000;
      font-weight: bold;
    }
    
    /* Override dashboard CSS for proper layout */
    body {
      display: block !important;
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }
    
    .container {
      display: block !important;
      margin-left: 250px !important;
      min-height: 100vh;
      width: calc(100% - 250px) !important;
      padding: 0 !important;
    }
    
    .main-content {
      height: auto !important;
      min-height: calc(100vh - 100px);
      padding: 0 !important;
      margin: 0 !important;
      background-color: #f8f9fa;
    }
    
    /* Mobile responsive fixes */
    @media (max-width: 768px) {
      body {
        padding: 0 !important;
        margin: 0 !important;
      }
      
      .container {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 0 !important;
      }
      
      .main-content {
        padding: 0 !important;
        margin: 0 !important;
      }
      
      .sidebar {
        display: none !important; /* Hide sidebar on mobile */
      }
      
      /* Show top navigation on mobile only */
      .top-nav {
        display: flex !important;
      }
      
      .nav-tabs {
        display: flex !important;
      }
      
      /* Feedback form mobile styling */
      .feedback-wrapper {
        padding: 0 !important;
        margin: 0 !important;
      }
      
      .feedback-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      }
      
      .feedback-container h4 {
        font-size: 20px;
        margin-bottom: 15px;
      }
      
      .feedback-container p {
        font-size: 14px;
        margin-bottom: 20px;
      }
      
      .form-label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
      }
      
      .form-select, .form-control {
        padding: 12px 15px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #ddd;
      }
      
      .star-rating {
        margin: 15px 0;
      }
      
      .stars {
        justify-content: center;
        gap: 10px;
      }
      
      .star {
        font-size: 28px;
      }
      
      .rating-text {
        text-align: center;
        font-size: 14px;
        margin-top: 10px;
      }
      
      .btn-lanka {
        width: 100%;
        padding: 15px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        margin-top: 20px;
      }
    }
    
    /* Top Navigation Bar - MOBILE ONLY */
    .top-nav {
      background: linear-gradient(135deg, #8B0000, #A52A2A);
      color: white;
      padding: 15px 20px;
      display: none; /* Hidden on desktop */
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    .top-nav .logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .top-nav .logo-section img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    
    .top-nav .user-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .top-nav .user-section img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      border: 2px solid white;
    }
    
    .nav-tabs {
      display: none; /* Hidden on desktop */
      justify-content: center;
      background: #8B0000;
      padding: 0;
      margin: 0;
      border-bottom: 3px solid #A52A2A;
    }
    
    .nav-tabs a {
      flex: 1;
      text-align: center;
      padding: 15px 10px;
      color: white;
      text-decoration: none;
      border-right: 1px solid rgba(255,255,255,0.2);
      transition: background-color 0.3s ease;
      font-weight: 500;
    }
    
    .nav-tabs a:last-child {
      border-right: none;
    }
    
    .nav-tabs a.active,
    .nav-tabs a:hover {
      background: rgba(255,255,255,0.1);
    }
    
    .nav-tabs a.active {
      background: rgba(255,255,255,0.2);
      border-bottom: 3px solid white;
    }
    
    /* Dropdown functionality */
    .user-dropdown {
      position: relative;
      display: inline-block;
    }
    
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background-color: white;
      min-width: 120px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      border-radius: 5px;
      z-index: 1001;
      margin-top: 5px;
    }
    
    .dropdown-content a {
      color: #333 !important;
      padding: 12px 16px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      border-radius: 5px;
    }
    
    .dropdown-content a:hover {
      background-color: #f1f1f1;
    }
    
    .user-dropdown.show .dropdown-content {
      display: block;
    }
    
    .user-section {
      cursor: pointer;
    }
      margin: 0 !important;
    }
    
    .feedback-wrapper {
      padding-left: 0 !important;
      margin-left: 0 !important;
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
      <a href="feedback.php" class="active">
        <i class="fas fa-comment-alt"></i> Feedback
      </a>
      <a href="incidents.php">
        <i class="fas fa-exclamation-triangle"></i> Report Incident
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
          <li class="active"><a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
          <li><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
        </ul>
      </nav>
      <div class="logout">
        <a href="../auth/Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
    
    <div class="main-content">

<div class="feedback-wrapper">
  <div class="feedback-container">
    <h4 class="mb-3 text-center">Feedback Form</h4>
    <p class="text-muted mb-4 text-center">
      We value your opinion! Help us improve the LankaTransit experience by sharing your thoughts.
    </p>

    <form action="submit_feedback.php" method="POST">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

      <div class="mb-3">
        <label for="booking_id" class="form-label">Select Your Previous Trip</label>
        <select name="booking_id" id="booking_id" class="form-select" required>
          <option value="">-- Select a Trip to Review --</option>
          <?php if (!empty($userBookings)): ?>
            <?php foreach ($userBookings as $booking): ?>
              <option value="<?= $booking['BookingID'] ?>">
                <?= htmlspecialchars($booking['BookingRef']) ?> - 
                Bus <?= htmlspecialchars($booking['BusNumber']) ?> | 
                <?= htmlspecialchars($booking['Origin'] ?? 'N/A') ?> → <?= htmlspecialchars($booking['Destination'] ?? 'N/A') ?> | 
                Seat <?= htmlspecialchars($booking['SeatNumber']) ?> | 
                <?= date('M j, Y', strtotime($booking['BookingTime'])) ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="" disabled>No previous trips found. Make a booking first to leave feedback.</option>
          <?php endif; ?>
        </select>
        <?php if (empty($userBookings)): ?>
          <div class="form-text text-muted">
            <i class="fas fa-info-circle"></i> 
            You need to have completed trips before you can leave feedback. 
            <a href="../index.php" class="text-decoration-none">Book a trip now</a>
          </div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label for="rating" class="form-label">Rating</label>
        <div class="star-rating">
          <input type="hidden" name="rating" id="rating" required>
          <div class="stars" id="starContainer">
            <i class="fas fa-star star" data-rating="1"></i>
            <i class="fas fa-star star" data-rating="2"></i>
            <i class="fas fa-star star" data-rating="3"></i>
            <i class="fas fa-star star" data-rating="4"></i>
            <i class="fas fa-star star" data-rating="5"></i>
          </div>
          <div class="rating-text" id="ratingText">Click stars to rate</div>
        </div>
      </div>

      <div class="mb-3">
        <label for="comment" class="form-label">Comments (Optional)</label>
        <textarea name="comment" id="comment" rows="5" class="form-control" placeholder="Write your comments here..."></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn-lanka">Submit Feedback</button>
      </div>
    </form>
  </div>
</div>

    </div> <!-- End main-content -->
  </div> <!-- End container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.getElementById('ratingText');
    const ratingLabels = {
        1: 'Poor - Needs significant improvement',
        2: 'Fair - Below expectations',
        3: 'Good - Meets expectations',
        4: 'Very Good - Exceeds expectations',
        5: 'Excellent - Outstanding service!'
    };
    
    let selectedRating = 0;
    
    stars.forEach((star, index) => {
        // Click event
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
            ratingText.textContent = ratingLabels[selectedRating];
            ratingText.classList.add('rating-selected');
        });
        
        // Hover events
        star.addEventListener('mouseenter', function() {
            if (selectedRating === 0) {
                const hoverRating = parseInt(this.dataset.rating);
                updateStars(hoverRating, true);
                ratingText.textContent = ratingLabels[hoverRating];
            }
        });
        
        star.addEventListener('mouseleave', function() {
            if (selectedRating === 0) {
                updateStars(0);
                ratingText.textContent = 'Click stars to rate';
            }
        });
    });
    
    function updateStars(rating, isHover = false) {
        stars.forEach((star, index) => {
            star.classList.remove('active', 'hovered');
            if (index < rating) {
                if (isHover && selectedRating === 0) {
                    star.classList.add('hovered');
                } else if (!isHover) {
                    star.classList.add('active');
                }
            }
        });
    }
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (selectedRating === 0) {
            e.preventDefault();
            alert('Please select a rating before submitting your feedback.');
            return false;
        }
    });
});

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

<script src="../assets/js/user-dashboard.js"></script>
</body>
</html>
