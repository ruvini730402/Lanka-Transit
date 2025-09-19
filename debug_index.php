<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Simple error logging
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'debug.log');
}

try {
    logError("Starting index.php execution");
    
    // Test basic PHP functionality
    logError("PHP version: " . phpversion());
    
    // Check if files exist
    $requiredFiles = [
        'classes/Announcement.php',
        'classes/Database.php',
        'config/env_loader.php',
        'config/database_config.php',
        'includes/header.php'
    ];
    
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            logError("Missing file: $file");
            die("Error: Required file '$file' not found. Check deployment.");
        } else {
            logError("File exists: $file");
        }
    }
    
    // Test database connectivity first
    logError("Testing database connection...");
    require_once 'config/env_loader.php';
    
    // Load environment
    EnvLoader::load();
    logError("Environment loaded");
    
    // Test database config
    require_once 'config/database_config.php';
    logError("Database config loaded");
    
    // Test database connection
    require_once 'classes/Database.php';
    $testDB = new Database();
    $testConn = $testDB->getConnection();
    
    if ($testConn) {
        logError("Database connection successful");
    } else {
        logError("Database connection failed");
        die("Database connection error. Check your database configuration.");
    }
    
    // Test announcement class
    logError("Loading Announcement class...");
    require_once 'classes/Announcement.php';
    $announcement = new Announcement();
    logError("Announcement class loaded");
    
    // Get announcements with error handling
    $recentAnnouncements = [];
    try {
        $recentAnnouncements = $announcement->getRecentAnnouncements(3);
        logError("Announcements retrieved: " . count($recentAnnouncements));
    } catch (Exception $e) {
        logError("Error getting announcements: " . $e->getMessage());
        $recentAnnouncements = []; // Continue with empty array
    }
    
    // Include header
    logError("Including header...");
    include 'includes/header.php';
    logError("Header included successfully");
    
} catch (Exception $e) {
    logError("Fatal error: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    
    // Display user-friendly error
    echo "<!DOCTYPE html><html><head><title>Site Maintenance</title></head><body>";
    echo "<h1>Site Under Maintenance</h1>";
    echo "<p>We're experiencing technical difficulties. Please try again later.</p>";
    echo "<p>Error details have been logged for investigation.</p>";
    echo "</body></html>";
    exit;
}
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
                                <div class="col-md-6 col-lg-3">
                                    <label for="date" class="form-label fw-semibold">
                                        <i class="fas fa-calendar-alt text-warning me-2"></i>Date
                                    </label>
                                    <input type="date" class="form-select" id="date" name="date" 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                                </div>
                                <div class="col-md-6 col-lg-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
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
        <div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-3">
                        <i class="fas fa-bullhorn text-primary me-2"></i>Latest Announcements
                    </h3>
                    <div class="row">
                        <?php foreach ($recentAnnouncements as $announcement): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($announcement['Title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($announcement['Content'], 0, 100)) . '...'; ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('M j, Y', strtotime($announcement['CreatedAt'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Features Section -->
        <div class="container mt-5">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                        <h4>Real-time Updates</h4>
                        <p>Get live updates on bus schedules and seat availability</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                        <h4>Secure Booking</h4>
                        <p>Safe and secure payment processing with PayHere</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt fa-3x text-warning mb-3"></i>
                        <h4>Mobile Friendly</h4>
                        <p>Book your tickets on any device, anywhere, anytime</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Include footer
include 'includes/footer.php';
?>