<?php
// Start the session at the top of the file
session_start();

// Include announcement class and fetch recent announcements
require_once 'classes/Announcement.php';
$announcement = new Announcement();
$recentAnnouncements = $announcement->getRecentAnnouncements(3); // Get 3 most recent announcements
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
            padding: 100px 0 80px 0;
            position: relative;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>') no-repeat;
            background-size: cover;
        }
        .search-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            padding: 35px;
            margin-top: -60px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(0, 0, 0, 0.05);
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
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 30px 0;
            width: 100%;
            text-align: center;
        }
        .announcements-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 60px 0;
        }
        .announcement-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 25px;
            border-left: 5px solid #800000;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .announcement-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(128, 0, 0, 0.05) 0%, transparent 50%);
            border-radius: 0 0 0 100px;
        }
        .announcement-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        .announcement-title {
            color: #800000;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .announcement-message {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .announcement-date {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .announcement-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #800000 0%, #4B0000 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .no-announcements {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
        }
        
        /* Enhanced Navigation Styles */
        .navbar {
            padding: 1rem 0;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            transition: color 0.3s ease;
        }
        .navbar-brand:hover {
            color: #4B0000 !important;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.75rem 1rem !important;
            margin: 0 0.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #374151 !important;
            position: relative;
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(128, 0, 0, 0.08);
            color: #800000 !important;
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background-color: rgba(128, 0, 0, 0.1);
            color: #800000 !important;
            font-weight: 600;
        }
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: linear-gradient(135deg, #800000 0%, #4B0000 100%);
            border-radius: 2px;
        }
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }
        .navbar-toggler:hover {
            background-color: rgba(128, 0, 0, 0.05);
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 0.75rem 0;
            margin-top: 0.5rem;
            min-width: 200px;
        }
        .dropdown-item {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 0;
        }
        .dropdown-item:hover {
            background-color: rgba(128, 0, 0, 0.08);
            color: #800000;
            padding-left: 1.5rem;
        }
        .dropdown-item i {
            width: 18px;
            text-align: center;
            opacity: 0.7;
        }
        .dropdown-divider {
            margin: 0.5rem 1rem;
            opacity: 0.1;
        }
        .nav-item.dropdown .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-nav .dropdown-toggle::after {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }
        .navbar-nav .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        /* Responsive Navigation */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding-top: 1rem;
                border-top: 1px solid rgba(0, 0, 0, 0.08);
                margin-top: 1rem;
            }
            .navbar-nav .nav-link {
                padding: 0.75rem 0 !important;
                margin: 0;
                border-radius: 0;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }
            .navbar-nav .nav-link:last-child {
                border-bottom: none;
            }
            .dropdown-menu {
                background-color: rgba(248, 249, 250, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(0, 0, 0, 0.05);
                margin-left: 1rem;
            }
        }
        
        /* Scroll Effect */
        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="fw-bold" style="color: #800000;">Transit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/announcements.php">
                            <i class="fas fa-bullhorn me-1"></i>Announcements
                        </a>
                    </li>
                    <?php if (isset($_SESSION['email']) && isset($_SESSION['role'])): ?>
                        <!-- User is logged in -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2">
                                        <i class="fas fa-user-circle" style="font-size: 1.25rem; color: #800000;"></i>
                                    </div>
                                    <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo $_SESSION['role'] === 'admin' ? 'Admin/admin.html' : 'pages/dashboard.php'; ?>">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="pages/my-bookings.php">
                                        <i class="fas fa-history me-2"></i>My Bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="pages/profile.php">
                                        <i class="fas fa-user-edit me-2"></i>Profile Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="auth/Logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">
                                <i class="fas fa-user-plus me-1"></i>Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" style="padding-top: 80px;">
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

        <!-- Announcements Section -->
        <?php if (!empty($recentAnnouncements)): ?>
        <section class="announcements-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold" style="color: #800000;">
                                <i class="fas fa-bullhorn me-3"></i>Latest Announcements
                            </h2>
                            <p class="lead text-muted">Stay updated with our latest news and important information</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="row">
                            <?php foreach ($recentAnnouncements as $announcement): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="announcement-card h-100">
                                    <div class="announcement-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div style="position: relative; z-index: 2;">
                                        <h5 class="announcement-title">
                                            <?php echo htmlspecialchars($announcement['title']); ?>
                                        </h5>
                                        <div class="announcement-message">
                                            <?php 
                                            $message = htmlspecialchars($announcement['message']);
                                            // Limit message length for card display
                                            if (strlen($message) > 150) {
                                                $message = substr($message, 0, 150) . '...';
                                            }
                                            echo nl2br($message);
                                            ?>
                                        </div>
                                        <div class="announcement-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <?php 
                                            $date = new DateTime($announcement['created_at']);
                                            echo $date->format('M j, Y g:i A'); 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- View All Announcements Link -->
                <div class="text-center mt-4">
                    <a href="pages/announcements.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-list me-2"></i>View All Announcements
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Features Section -->
        <section class="py-5 mt-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; position: relative;">
                            <div class="card-body p-4" style="position: relative; z-index: 2;">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Safe & Secure</h5>
                                <p class="card-text">Your bookings and payments are protected with advanced security measures.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; position: relative;">
                            <div class="card-body p-4" style="position: relative; z-index: 2;">
                                <i class="fas fa-clock fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Real-time Updates</h5>
                                <p class="card-text">Get live updates on bus schedules, delays, and seat availability.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; position: relative;">
                            <div class="card-body p-4" style="position: relative; z-index: 2;">
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
            <div class="mb-3">
                <i class="fas fa-bus fa-2x" style="opacity: 0.8;"></i>
            </div>
            <p class="mb-0 fw-500">&copy; 2025 Transit. All rights reserved.</p>
            <p class="mb-0 small text-white-50 mt-2">Your trusted partner for bus travel</p>
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
                alert('Please choose different cities for your origin and destination!');
                return false;
            }
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            if (travelDate < today) {
                e.preventDefault();
                alert('Please select today or a future date for your travel!');
                return false;
            }
        });
        
        // Set default date to today
        document.getElementById('travel_date').value = new Date().toISOString().split('T')[0];
        
        // Enhanced Navigation Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Enhanced mobile menu interaction
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        navbarToggler.addEventListener('click', function() {
            setTimeout(() => {
                if (navbarCollapse.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }, 300);
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                    document.body.style.overflow = '';
                }
            }
        });
    </script>
</body>
</html>