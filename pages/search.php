<?php
require_once '../classes/Database.php';
require_once '../classes/Bus.php';

// Initialize variables
$searchResults = [];
$error = '';
$searchPerformed = false;
$filters = [];
$fareRange = ['min_fare' => 0, 'max_fare' => 5000];
$timeRange = ['earliest_time' => '05:00', 'latest_time' => '23:00'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchPerformed = true;
    
    try {
        // Get form data
        $origin = isset($_POST['origin']) ? $_POST['origin'] : '';
        $destination = isset($_POST['destination']) ? $_POST['destination'] : '';
        $travelDate = isset($_POST['travel_date']) ? $_POST['travel_date'] : '';
        
        // Get filters
        $filters = [
            'min_fare' => isset($_POST['min_fare']) && !empty($_POST['min_fare']) ? (float)$_POST['min_fare'] : null,
            'max_fare' => isset($_POST['max_fare']) && !empty($_POST['max_fare']) ? (float)$_POST['max_fare'] : null,
            'departure_time_from' => isset($_POST['departure_time_from']) && !empty($_POST['departure_time_from']) ? $_POST['departure_time_from'] : null,
            'departure_time_to' => isset($_POST['departure_time_to']) && !empty($_POST['departure_time_to']) ? $_POST['departure_time_to'] : null,
            'min_seats' => isset($_POST['min_seats']) && !empty($_POST['min_seats']) ? (int)$_POST['min_seats'] : null,
            'sort_by' => isset($_POST['sort_by']) ? $_POST['sort_by'] : 'departure_time'
        ];
        
        // Basic required field check
        if (empty($origin) || empty($destination) || empty($travelDate)) {
            $error = 'Please fill in all required fields to search for buses.';
        } else {
            // Initialize database connection
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                if ($db) {
                    // Create Bus object and search
                    $bus = new Bus($db);
                    
                    // Get fare and time ranges for this route
                    $fareRange = $bus->getFareRange($origin, $destination);
                    $timeRange = $bus->getDepartureTimeRange($origin, $destination);
                    
                    // Perform search with filters
                    $result = $bus->searchBusesWithFilters($origin, $destination, $travelDate, $filters);
                    
                    if (isset($result['error'])) {
                        $error = $result['error'];
                    } else {
                        $searchResults = $result['data'];
                    }
                } else {
                    $error = 'Unable to connect to the booking system. Please check your internet connection and try again.';
                }
            } catch (PDOException $e) {
                $error = 'We are experiencing technical difficulties with our booking system. Please try again in a few minutes.';
                error_log("Search database error: " . $e->getMessage());
            } catch (Exception $e) {
                $error = 'Something went wrong while searching for buses. Please refresh the page and try again.';
                error_log("Search general error: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        $error = 'An error occurred while processing your request. Please try again.';
        error_log("Form processing error: " . $e->getMessage());
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Additional CSS for Search Page -->
<link rel="stylesheet" href="../assets/css/search.css">

<!-- Search Header -->
    <section class="search-header" style="padding-top: 100px;">
        <div class="container text-center">
            <h1 class="h2 mb-3">Bus Search Results</h1>
            <?php if ($searchPerformed && !$error): ?>
                <p class="mb-0">
                    <i class="fas fa-route me-2"></i>
                    <?php echo htmlspecialchars($origin); ?> → <?php echo htmlspecialchars($destination); ?>
                    <span class="mx-3">|</span>
                    <i class="fas fa-calendar me-2"></i>
                    <?php echo date('F j, Y', strtotime($travelDate)); ?>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <div class="container">
        <?php if ($searchPerformed && !$error): ?>
            <!-- Search Summary -->
            <div class="search-summary">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1">
                            <?php echo count($searchResults); ?> 
                            <?php echo count($searchResults) === 1 ? 'bus' : 'buses'; ?> found
                        </h5>
                        <small class="text-muted">
                            <?php echo htmlspecialchars($origin); ?> to <?php echo htmlspecialchars($destination); ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="../index.php" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>New Search
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filters-section mt-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filter & Sort Results
                            <button class="btn btn-sm btn-outline-secondary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h6>
                    </div>
                    <div class="collapse show" id="filtersCollapse">
                        <div class="card-body">
                            <form method="POST" id="filtersForm">
                                <!-- Hidden fields to maintain search criteria -->
                                <input type="hidden" name="origin" value="<?php echo htmlspecialchars($origin); ?>">
                                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                                <input type="hidden" name="travel_date" value="<?php echo htmlspecialchars($travelDate); ?>">

                                <div class="row g-3">
                                    <!-- Sort Options -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-sort me-1"></i>Sort By
                                        </label>
                                        <select name="sort_by" class="form-select">
                                            <option value="departure_early" <?php echo ($filters['sort_by'] ?? '') === 'departure_early' ? 'selected' : ''; ?>>
                                                Departure (Early First)
                                            </option>
                                            <option value="departure_late" <?php echo ($filters['sort_by'] ?? '') === 'departure_late' ? 'selected' : ''; ?>>
                                                Departure (Late First)
                                            </option>
                                            <option value="fare_low" <?php echo ($filters['sort_by'] ?? '') === 'fare_low' ? 'selected' : ''; ?>>
                                                Price (Low to High)
                                            </option>
                                            <option value="fare_high" <?php echo ($filters['sort_by'] ?? '') === 'fare_high' ? 'selected' : ''; ?>>
                                                Price (High to Low)
                                            </option>
                                            <option value="seats_available" <?php echo ($filters['sort_by'] ?? '') === 'seats_available' ? 'selected' : ''; ?>>
                                                Most Seats Available
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Fare Range -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-money-bill-wave me-1"></i>Fare Range
                                        </label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="number" name="min_fare" class="form-control" 
                                                       placeholder="Min Rs." min="0" max="<?php echo $fareRange['max_fare']; ?>"
                                                       value="<?php echo $filters['min_fare'] ?? ''; ?>">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="max_fare" class="form-control" 
                                                       placeholder="Max Rs." min="0" max="<?php echo $fareRange['max_fare']; ?>"
                                                       value="<?php echo $filters['max_fare'] ?? ''; ?>">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            Range: Rs. <?php echo number_format($fareRange['min_fare'], 0); ?> - Rs. <?php echo number_format($fareRange['max_fare'], 0); ?>
                                        </small>
                                    </div>

                                    <!-- Departure Time -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-clock me-1"></i>Departure Time
                                        </label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="time" name="departure_time_from" class="form-control"
                                                       value="<?php echo $filters['departure_time_from'] ?? ''; ?>">
                                            </div>
                                            <div class="col-6">
                                                <input type="time" name="departure_time_to" class="form-control"
                                                       value="<?php echo $filters['departure_time_to'] ?? ''; ?>">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            Available: <?php echo $timeRange['earliest_time']; ?> - <?php echo $timeRange['latest_time']; ?>
                                        </small>
                                    </div>

                                    <!-- Seat Availability -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-chair me-1"></i>Minimum Seats
                                        </label>
                                        <select name="min_seats" class="form-select">
                                            <option value="">Any</option>
                                            <option value="1" <?php echo ($filters['min_seats'] ?? '') == '1' ? 'selected' : ''; ?>>At least 1 seat</option>
                                            <option value="5" <?php echo ($filters['min_seats'] ?? '') == '5' ? 'selected' : ''; ?>>At least 5 seats</option>
                                            <option value="10" <?php echo ($filters['min_seats'] ?? '') == '10' ? 'selected' : ''; ?>>At least 10 seats</option>
                                            <option value="20" <?php echo ($filters['min_seats'] ?? '') == '20' ? 'selected' : ''; ?>>At least 20 seats</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Filter Buttons -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-2"></i>Apply Filters
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                                <i class="fas fa-undo me-2"></i>Clear All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Area -->
            <div class="mt-4">
            <!-- Results Content -->
            <?php if (empty($searchResults)): ?>
                <div class="no-results">
                    <i class="fas fa-bus fa-4x mb-3 text-muted"></i>
                    <h4>No buses available</h4>
                    <p>We couldn't find any buses matching your criteria. Try adjusting your filters or selecting a different date.</p>
                    <button class="btn btn-primary" onclick="clearFilters()">Clear All Filters</button>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($searchResults as $bus): ?>
                        <div class="col-xl-6 mb-4">
                            <div class="card bus-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <i class="fas fa-bus text-primary me-2"></i>
                                                Bus <?php echo htmlspecialchars($bus['bus_number']); ?>
                                            </h5>
                                            <small class="text-muted">Capacity: <?php echo $bus['capacity']; ?> seats</small>
                                        </div>
                                        <span class="badge fare-badge">
                                            Rs. <?php echo number_format($bus['fare'], 2); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Departure</small>
                                            <strong><?php echo date('g:i A', strtotime($bus['departure_time'])); ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Arrival</small>
                                            <strong><?php echo date('g:i A', strtotime($bus['arrival_time'])); ?></strong>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($bus['stops'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Stops</small>
                                            <small><?php echo htmlspecialchars($bus['stops']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="available-seats">
                                            <i class="fas fa-chair me-1"></i>
                                            <?php echo $bus['available_seats']; ?> seats available
                                        </span>
                                        <a href="seatbooking.php?bus_id=<?php echo $bus['bus_id']; ?>&date=<?php echo urlencode($travelDate); ?>&origin=<?php echo urlencode($origin); ?>&destination=<?php echo urlencode($destination); ?>&fare=<?php echo $bus['fare']; ?>&bus_number=<?php echo urlencode($bus['bus_number']); ?>&departure=<?php echo urlencode($bus['departure_time']); ?>&arrival=<?php echo urlencode($bus['arrival_time']); ?>" 
                                           class="btn btn-success">
                                            <i class="fas fa-ticket-alt me-1"></i>Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <hr>
                <a href="../index.php" class="btn btn-outline-danger">Go Back to Search</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Filters Overlay for Mobile -->
    <!-- No longer needed with horizontal filters -->

    <!-- Search Page JavaScript -->
    <script src="../assets/js/search.js"></script>

<?php include '../includes/footer.php'; ?>