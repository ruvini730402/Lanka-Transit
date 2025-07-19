<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanka Transit - Bus Seat Booking</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="public/css/style.css" rel="stylesheet">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Book bus seats online in Sri Lanka. Find and reserve your bus tickets for intercity travel with Lanka Transit.">
    <meta name="keywords" content="bus booking, Sri Lanka, intercity bus, online booking, travel">
    <meta name="author" content="Lanka Transit">
    
    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:title" content="Lanka Transit - Bus Seat Booking">
    <meta property="og:description" content="Book bus seats online in Sri Lanka. Find and reserve your bus tickets for intercity travel.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="resources/logo.jpeg">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="resources/logo.jpeg" alt="Lanka Transit Logo" class="logo">
                Lanka Transit
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-ticket-alt me-1"></i> My Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="routes.php">
                            <i class="fas fa-route me-1"></i> Routes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-phone me-1"></i> Contact
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Container -->
    <div id="alertContainer" class="container mt-3"></div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center hero-content">
                    <h1 class="hero-title">Find Your Perfect Bus Journey</h1>
                    <p class="hero-subtitle">
                        Book comfortable and reliable bus tickets across Sri Lanka. 
                        Compare prices, check availability, and reserve your seats instantly.
                    </p>
                </div>
            </div>
            
            <!-- Search Form -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="searchForm" class="search-form" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <label for="origin" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i> From
                                </label>
                                <select class="form-select" id="origin" name="origin" required>
                                    <option value="">Select Origin</option>
                                    <!-- Options loaded dynamically -->
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <label for="destination" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i> To
                                </label>
                                <select class="form-select" id="destination" name="destination" required>
                                    <option value="">Select Destination</option>
                                    <!-- Options loaded dynamically -->
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-lg-2">
                                <label for="date" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i> Date
                                </label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            
                            <div class="col-md-6 col-lg-2">
                                <label for="maxFare" class="form-label">
                                    <i class="fas fa-money-bill-wave me-1"></i> Max Fare (Rs.)
                                </label>
                                <input type="number" class="form-control" id="maxFare" name="max_fare" 
                                       placeholder="Any" min="0" step="10">
                            </div>
                            
                            <div class="col-lg-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary search-btn">
                                    <i class="fas fa-search me-1"></i> Search Buses
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results Section -->
    <section class="search-results" id="searchResults" style="display: none;">
        <div class="container">
            <!-- Results will be loaded dynamically here -->
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="mb-3">Why Choose Lanka Transit?</h2>
                    <p class="text-muted">Experience the best in bus travel booking with our advanced platform</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-clock fa-3x text-primary-custom"></i>
                        </div>
                        <h5>Real-time Availability</h5>
                        <p class="text-muted">Check live seat availability and book instantly</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary-custom"></i>
                        </div>
                        <h5>Secure Booking</h5>
                        <p class="text-muted">Safe and secure payment processing</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-mobile-alt fa-3x text-primary-custom"></i>
                        </div>
                        <h5>Mobile Friendly</h5>
                        <p class="text-muted">Book from anywhere using your mobile device</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-headset fa-3x text-primary-custom"></i>
                        </div>
                        <h5>24/7 Support</h5>
                        <p class="text-muted">Round-the-clock customer support</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Routes Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="mb-3">Popular Routes</h2>
                    <p class="text-muted">Most searched bus routes in Sri Lanka</p>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-route text-primary-custom me-2"></i>
                                Colombo → Kandy
                            </h6>
                            <p class="card-text text-muted small">
                                Popular route connecting the commercial capital to the cultural capital
                            </p>
                            <small class="text-primary-custom">From Rs. 200</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-route text-primary-custom me-2"></i>
                                Colombo → Galle
                            </h6>
                            <p class="card-text text-muted small">
                                Scenic coastal route to the historic southern city
                            </p>
                            <small class="text-primary-custom">From Rs. 150</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-route text-primary-custom me-2"></i>
                                Kandy → Nuwara Eliya
                            </h6>
                            <p class="card-text text-muted small">
                                Mountain route through tea plantations
                            </p>
                            <small class="text-primary-custom">From Rs. 100</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <h5 class="mb-3">Lanka Transit</h5>
                    <p class="text-muted">
                        Your trusted partner for comfortable and reliable bus travel across Sri Lanka.
                    </p>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="routes.php">Routes</a></li>
                        <li><a href="booking.php">My Bookings</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <h6 class="mb-3">Support</h6>
                    <ul class="footer-links">
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="terms.php">Terms & Conditions</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <h6 class="mb-3">Contact Info</h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone me-2"></i> +94 11 123 4567
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i> info@lankatransit.lk
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i> Colombo, Sri Lanka
                    </p>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?= date('Y') ?> Lanka Transit. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="public/js/search.js"></script>
</body>
</html>
