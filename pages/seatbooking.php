<?php
require_once '../includes/session_config.php';

// Get booking parameters from URL
$bus_id = $_GET['bus_id'] ?? '';
$travel_date = $_GET['date'] ?? '';
$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$fare = $_GET['fare'] ?? '';
$bus_number = $_GET['bus_number'] ?? '';
$departure_time = $_GET['departure'] ?? '';
$arrival_time = $_GET['arrival'] ?? '';

// Validate required parameters
if (empty($bus_id) || empty($travel_date) || empty($origin) || empty($destination)) {
    header('Location: ../index.php?error=missing_params');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Seat Booking</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/seatbooking.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../assets/images/lankalogo.png" alt="Lanka Transit Logo" style="height: 40px; margin-right: 10px;">
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
            </ul>
        </div>
    </div>
</nav>
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Find Your Perfect Journey</h1>
            <p class="lead mb-4">Book bus tickets with ease and comfort</p>
        </div>
    </section>
  <div class="container my-4">
 <h2 class="text-center mb-4 title-dark-blue">Bus Seat Selection</h2>
 
 <!-- Booking Summary -->
 <div class="row mb-4">
   <div class="col-12">
     <div class="card shadow-sm">
       <div class="card-body">
         <h5 class="card-title title-dark-blue">Journey Details</h5>
         <div class="row">
           <div class="col-md-3">
             <strong>Route:</strong><br>
             <?php echo htmlspecialchars($origin); ?> → <?php echo htmlspecialchars($destination); ?>
           </div>
           <div class="col-md-3">
             <strong>Date:</strong><br>
             <?php echo date('F j, Y', strtotime($travel_date)); ?>
           </div>
           <div class="col-md-3">
             <strong>Bus:</strong><br>
             <?php echo htmlspecialchars($bus_number); ?>
           </div>
           <div class="col-md-3">
             <strong>Fare:</strong><br>
             Rs. <?php echo number_format($fare, 2); ?>
           </div>
         </div>
         <?php if ($departure_time): ?>
         <div class="row mt-2">
           <div class="col-md-6">
             <strong>Departure:</strong> <?php echo date('g:i A', strtotime($departure_time)); ?>
           </div>
           <div class="col-md-6">
             <strong>Arrival:</strong> <?php echo date('g:i A', strtotime($arrival_time)); ?>
           </div>
         </div>
         <?php endif; ?>
       </div>
     </div>
   </div>
 </div>

 <!-- Bus Model Selection -->
 <div class="row mb-4">
   <div class="col-12">
     <div class="card shadow-sm">
       <!-- <div class="card-body">
         <h5 class="card-title title-dark-blue">Choose Bus Model</h5>
         <select id="bus-model" class="form-select">
           <option value="49">49 Seater Model</option>
           <option value="54">54 Seater Model</option>
         </select>
       </div> -->
     </div>
   </div>
 </div>

 <div class="row">
  <!-- Left: Seat Map -->
  <div class="col-md-7">
    <div id="seat-map" class="bus-interior">
      <div class="bus-front">
        <div class="entrance-area">🚪 Front Entrance</div>
        <div class="driver-area">🚍 Driver</div>
      </div>
      <div class="seat-grid"></div>
      <!-- <div class="bus-rear">
        <div class="rear-entrance">🚪 Rear Entrance</div>
      </div> -->
    </div>
  </div>

  <!-- Right: Passenger Info Form + Legend -->
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3 title-dark-blue">Passenger Info</h5>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <div id="form-message"></div>
        <form id="booking-form" method="POST" action="book.php">
          <div class="mb-3">
            <label class="form-label title-dark-blue">Passenger Name</label>
            <input type="text" class="form-control" name="name" required pattern="[A-Za-z\s]+" title="Name should contain only letters and spaces.">
          </div>

          <div class="mb-3">
            <label class="form-label title-dark-blue">Mobile Number</label>
            <input type="tel" class="form-control" name="phone" required pattern="07\d{8}" title="Mobile number should start with 07 followed by 8 digits.">
          </div>

          <div class="mb-3">
            <label class="form-label title-dark-blue">NIC (Optional)</label>
            <input type="text" class="form-control" name="nic" pattern="^([0-9]{9}[vVxX]|[0-9]{12})$" title="Valid NIC format: 9 digits + V/X or 12 digits.">
          </div>

          <div class="mb-3">
            <label class="form-label title-dark-blue">Gender</label>
            <select class="form-select" name="gender" id="gender" required>
              <option value="">Select</option>
              <option value="female">Female</option>
              <option value="male">Male</option>
              <option value="undisclosed">Undisclosed</option>
            </select>
          </div>

          <input type="hidden" name="seat" id="selected-seat">
          <input type="hidden" name="model" id="selected-model">
          <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_id); ?>">
          <input type="hidden" name="travel_date" value="<?php echo htmlspecialchars($travel_date); ?>">
          <input type="hidden" name="origin" value="<?php echo htmlspecialchars($origin); ?>">
          <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
          <input type="hidden" name="fare" value="<?php echo htmlspecialchars($fare); ?>">
          <input type="hidden" name="bus_number" value="<?php echo htmlspecialchars($bus_number); ?>">
          <input type="hidden" name="departure_time" value="<?php echo htmlspecialchars($departure_time); ?>">
          <input type="hidden" name="arrival_time" value="<?php echo htmlspecialchars($arrival_time); ?>">
         <button type="submit" class="btn w-100" style="background-color: #800000; color: white;">Book Seat</button>
        </form>
      </div>
    </div>

    <!-- Legend -->
    <div class="mt-4">
      <h6 class="title-dark-blue">Legend:</h6>
      <div class="d-flex flex-column">
        <div class="d-flex align-items-center mb-2 title-dark-blue">
          <span class="d-inline-block me-2 seat-legend available"></span> Available
        </div>
        <div class="d-flex align-items-center mb-2 title-dark-blue">
          <span class="d-inline-block me-2 seat-legend booked female"></span> Booked (Female)
        </div>
        <div class="d-flex align-items-center mb-2 title-dark-blue">
          <span class="d-inline-block me-2 seat-legend booked male"></span> Booked (Male)
        </div>
        <div class="d-flex align-items-center mb-2 title-dark-blue">
          <span class="d-inline-block me-2 seat-legend booked undisclosed"></span> Booked (Undisclosed)
        </div>
        <div class="d-flex align-items-center mb-2 title-dark-blue">
          <span class="d-inline-block me-2 seat-legend lady-seat"></span> Lady Seats (1–8)
        </div>
        <div class="d-flex align-items-center mb-2 title-dark-blue">
          <span class="d-inline-block me-2 seat-legend selected"></span> Selected
        </div>
      </div>
    </div>
  </div>
</div>
  </div>
<footer class="footer-full text-center">
  <p class="mb-0">&copy; 2025 Transit. All rights reserved.</p>
</footer>

<script src="../assets/js/seatbooking.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>