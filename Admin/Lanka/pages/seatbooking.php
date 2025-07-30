<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bus Seat Booking</title>
  <!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  
  <link rel="stylesheet" href="../assets/booking.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../images/lankalogo.png" alt="Lanka Transit Logo" style="height: 40px; margin-right: 10px;">
            <span class="fw-bold" style="color: #800000;">Lanka Transit</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
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
 

 <div class="row">
  <!-- Left: Seat Map -->
  <div class="col-md-7">
    <div id="seat-map"></div>
  </div>

  <!-- Right: Passenger Info Form + Legend -->
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3 title-dark-blue">Passenger Info</h5>

        <div id="form-message"></div>
        <form id="booking-form">
          <div class="mb-3">
            <label class="form-label title-dark-blue ">Passenger Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>

          <div class="mb-3">
            <label class="form-label title-dark-blue">Mobile Number</label>
            <input type="tel" class="form-control" name="phone" required>
          </div>

          <div class="mb-3">
            <label class="form-label title-dark-blue">NIC (Optional)</label>
            <input type="text" class="form-control" name="nic">
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
         <button type="submit" class="btn w-100" style="background-color: #800000; color: white;">Book Seat</button>

        </form>
      </div>
    </div>

    <!-- Legend moved here -->
    <div class="mt-4">
      <h6 class="title-dark-blue">Legend:</h6>

      <div class="d-flex flex-column">
        <div class="d-flex align-items-center mb-1 title-dark-blue">
          <span class="d-inline-block rounded me-2" style="width:20px; height:20px; background:#90EE90;"></span> Available
        </div>
        <div class="d-flex align-items-center mb-1 title-dark-blue">
          <span class="d-inline-block rounded me-2" style="width:20px; height:20px; background:#ffb6c1;"></span> Booked (Female)
        </div>
        <div class="d-flex align-items-center mb-1 title-dark-blue">
          <span class="d-inline-block rounded me-2" style="width:20px; height:20px; background:#add8e6;"></span> Booked (Male)
        </div>
        <div class="d-flex align-items-center mb-1 title-dark-blue">
          <span class="d-inline-block rounded me-2" style="width:20px; height:20px; background:#A9A9A9;"></span> Booked (Undisclosed)
        </div>
          <div class="d-flex align-items-center mb-1 title-dark-blue">
  <span class="d-inline-block me-2" style="width:20px; height:20px; border:2px solid red;"></span> Lady Seats (1–8)
</div>

        <div class="d-flex align-items-center mb-1 title-dark-blue">
          <span class="d-inline-block rounded me-2" style="width:20px; height:20px; background: #FFA500 ;"></span> Selected
        </div>
      </div>
    </div>
  </div>
</div>
  </div>
<footer class="footer-full text-center">
  <p class="mb-0">&copy; 2025 Transit. All rights reserved.</p>
</footer>



<script src="../assets/booking.js"></script>
</body>
</html>







