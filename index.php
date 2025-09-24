<?php
// Start the session at the top of the file
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1 0 auto;
        }
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 80px 0;
        }
        .search-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .btn-search {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        footer {
            flex-shrink: 0;
            background-color: #800000;
            color: white;
            padding: 20px 0;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
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
                    <li class="nav-item">
                        <a class="nav-link" href="pages/booking.php">
                            <i class="fas fa-ticket-alt me-1"></i>Book Ticket
                        </a>
                    </li>
                    <?php if (isset($_SESSION['email']) && isset($_SESSION['role'])): ?>
                        <!-- User is logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $_SESSION['role'] === 'admin' ? 'Admin/admin.html' : 'pages/dashboard.php'; ?>">
                                <i class="fas fa-user me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/Logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="pages/login-form.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/register-form.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 fw-bold mb-3">Find Your Perfect Journey</h1>
                <p class="lead mb-4">Book bus tickets with ease and comfort</p>
            </div>
        </section>

        <!-- Search Form -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="search-card">
                        <form id="searchForm" method="POST" action="pages/search.php">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label for="origin" class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>From
                                    </label>
                                    <select class="form-select" id="origin" name="origin" required>
                                        <option value="">Select Origin</option>
                                        <option value="Badulla">Badulla</option>
                                        <option value="Ella">Ella</option>
                                        <option value="Wellawaya">Wellawaya</option>
                                        <option value="Thanamalvila">Thanamalvila</option>
                                        <option value="Lunugamvehera">Lunugamvehera</option>
                                        <option value="Tangalle">Tangalle</option>
                                        <option value="Dickwella">Dickwella</option>
                                        <option value="Devinuwara">Devinuwara</option>
                                        <option value="Matara">Matara</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label for="destination" class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>To
                                    </label>
                                    <select class="form-select" id="destination" name="destination" required>
                                        <option value="">Select Destination</option>
                                        <option value="Badulla">Badulla</option>
                                        <option value="Ella">Ella</option>
                                        <option value="Wellawaya">Wellawaya</option>
                                        <option value="Thanamalvila">Thanamalvila</option>
                                        <option value="Lunugamvehera">Lunugamvehera</option>
                                        <option value="Tangalle">Tangalle</option>
                                        <option value="Dickwella">Dickwella</option>
                                        <option value="Devinuwara">Devinuwara</option>
                                        <option value="Matara">Matara</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-2">
                                    <label for="travel_date" class="form-label fw-semibold">
                                        <i class="fas fa-calendar text-warning me-2"></i>Date
                                    </label>
                                    <input type="date" class="form-control" id="travel_date" name="travel_date" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6 col-lg-2">
                                    <label for="max_fare" class="form-label fw-semibold">
                                        <i class="fas fa-money-bill text-info me-2"></i>Max Fare
                                    </label>
                                    <select class="form-select" id="max_fare" name="max_fare">
                                        <option value="">Any Price</option>
                                        <option value="500">Under Rs. 500</option>
                                        <option value="1000">Under Rs. 1,000</option>
                                        <option value="1500">Under Rs. 1,500</option>
                                        <option value="2000">Under Rs. 2,000</option>
                                        <option value="3000">Under Rs. 3,000</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-search w-100">
                                        <i class="fas fa-search me-2"></i>Search Buses
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="py-5 mt-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Safe & Secure</h5>
                                <p class="card-text">Your bookings and payments are protected with advanced security measures.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <i class="fas fa-clock fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Real-time Updates</h5>
                                <p class="card-text">Get live updates on bus schedules, delays, and seat availability.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <i class="fas fa-mobile-alt fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Mobile Friendly</h5>
                                <p class="card-text">Book tickets on any device with our responsive design.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Transit. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script>
        // Form validation and XSS prevention
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const travelDate = document.getElementById('travel_date').value;

            // Basic validation
            if (origin === destination && origin !== '') {
                e.preventDefault();
                alert('Origin and destination cannot be the same!');
                return false;
            }
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            if (travelDate < today) {
                e.preventDefault();
                alert('Please select a valid travel date!');
                return false;
            }
        });
        
        // Set default date to today
        document.getElementById('travel_date').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>