<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Guest';
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
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
    <div class="sidebar">
      <div class="logo">
        <img src="../assets/images/uploads/dd.png" alt="LankaTransit Logo">
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
      <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
      </div>
      <nav class="navigation">
        <ul>
          <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
          <li><a href="incidents.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
          <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        </ul>
      </nav>
      <div class="logout">
        <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
    <div class="main-content">
      <div class="header-container">
        <div class="welcome-message">
          <span class="welcome-icon">👋</span>
          <h1>Hello, <?= $username ?>!</h1>
        </div>
      </div>

      <!-- Upcoming Bookings -->
      <div class="section">
        <h2><i class="fas fa-calendar-alt section-icon"></i> Upcoming Bookings</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Bus No.</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Booked Seats</th>
                <th>Fare (LKR)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>LTX9991</td>
                <td>NB-4533</td>
                <td>2025-07-24</td>
                <td>Matara</td>
                <td>Badulla</td>
                <td>B1, B2</td>
                <td>950</td>
              </tr>
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
                <th>Preferred Day</th>
                <th>Last Used</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Matara</td>
                <td>Badulla</td>
                <td>Friday</td>
                <td>2025-07-12</td>
                <td><form action="RebookTrip.php" method="post"><button class="rebook-btn">Book Again</button></form></td>
              </tr>
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
                <th>Booking ID</th>
                <th>Bus No.</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Booked Seats</th>
                <th>Fare (LKR)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>LTX8933</td>
                <td>NB-2345</td>
                <td>2025-07-21</td>
                <td>Matara</td>
                <td>Badulla</td>
                <td>A3, A4</td>
                <td>1200</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="section">
        <h2><i class="fas fa-redo section-icon"></i> Rebooking</h2>
        <div class="rebooking-card-wrapper">
          <div class="rebooking-card">
            <div class="rebooking-details">
              <i class="far fa-calendar-alt rebooking-icon"></i>
              <div>
                <p class="route">Matara &rarr; Badulla</p>
                <p class="last-traveled">Last traveled: 2025-07-20 • Seat No: A5</p>
              </div>
            </div>
            <button class="rebook-btn">Rebook Trip</button>
          </div>
        </div>
        <p class="hint">You can rebook your last journey with just one click.</p>
      </div>

      <div class="section">
        <h2><i class="fas fa-receipt section-icon"></i> Receipts</h2>
        <div class="receipt-card-wrapper">
          <div class="receipt-card">
            <div class="receipt-details">
              <i class="far fa-file-alt receipt-icon"></i>
              <div>
                <p class="receipt-booking-id">Booking #LTX8933</p>
                <p class="receipt-date">Date: 21 June 2025 • Matara &rarr; Badulla</p>
              </div>
            </div>
            <button class="download-btn">Download PDF</button>
          </div>
        </div>
        <p class="hint">Download your ticket receipts in PDF format for reference or refunds.</p>
      </div>
    </div>
  </div>
  <script src="../assets/js/user-dashboard.js"></script>
  
</body>
</html>
