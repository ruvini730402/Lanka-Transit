<?php
// Include necessary classes
require_once 'classes/Announcement.php';
require_once 'classes/Route.php';

// Fetch recent announcements
$announcement = new Announcement();
$recentAnnouncements = $announcement->getRecentAnnouncements(3); // Get 3 most recent announcements

// Fetch dynamic locations from Route class
$route = new Route();
$locationOptions = $route->getLocationsAsSelectOptions();

// Include header
include 'includes/header.php';
?>

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
                <div class="col-12">
                    <div class="search-card">
                        <form id="searchForm" method="POST" action="pages/search.php">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label for="origin" class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>From
                                    </label>
                                    <select class="form-select" id="origin" name="origin" required>
                                        <option value="">Select Origin</option>
                                        <?php echo $locationOptions; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label for="destination" class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>To
                                    </label>
                                    <select class="form-select" id="destination" name="destination" required>
                                        <option value="">Select Destination</option>
                                        <?php echo $locationOptions; ?>
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
                    <div class="col-12">
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
        <section class="features-section">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="card feature-card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <i class="fas fa-shield-alt fa-3x text-primary-custom mb-3"></i>
                                <h5 class="card-title">Safe & Secure</h5>
                                <p class="card-text">Your bookings and payments are protected with advanced security measures.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card feature-card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <i class="fas fa-clock fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Real-time Updates</h5>
                                <p class="card-text">Get live updates on bus schedules, delays, and seat availability.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card feature-card h-100 border-0 shadow-sm">
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

<?php include 'includes/footer.php'; ?>

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