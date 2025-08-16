<?php
require_once '../config/database.php';
require_once '../classes/Bus.php';

// Initialize variables
$searchResults = [];
$error = '';
$searchPerformed = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchPerformed = true;
    
    try {
        // Get form data (validation will be handled by Bus class)
        $origin = isset($_POST['origin']) ? $_POST['origin'] : '';
        $destination = isset($_POST['destination']) ? $_POST['destination'] : '';
        $travelDate = isset($_POST['travel_date']) ? $_POST['travel_date'] : '';
        $maxFare = isset($_POST['max_fare']) && !empty($_POST['max_fare']) ? (float)$_POST['max_fare'] : null;
        
        // Basic required field check only
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
                $result = $bus->searchBuses($origin, $destination, $travelDate, $maxFare);
                
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .search-header {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 40px 0;
        }
        .bus-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bus-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        .fare-badge {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .available-seats {
            color: #28a745;
            font-weight: bold;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .search-summary {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-top: -30px;
            position: relative;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-outline-primary {
            border-color: #800000;
            color: #800000;
            border-radius: 25px;
        }
        .btn-outline-primary:hover {
            background-color: #800000;
            border-color: #800000;
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
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
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-ticket-alt me-1"></i>Book Ticket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Header -->
    <section class="search-header">
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
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            <?php echo count($searchResults); ?> 
                            <?php echo count($searchResults) === 1 ? 'bus' : 'buses'; ?> found
                        </h5>
                        <small class="text-muted">
                            <?php echo htmlspecialchars($origin); ?> to <?php echo htmlspecialchars($destination); ?>
                            <?php if ($maxFare): ?>
                                • Max fare: Rs. <?php echo number_format($maxFare, 2); ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="../index.php" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>New Search
                        </a>
                    </div>
                </div>
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

        <!-- Search Results -->
        <?php if ($searchPerformed && !$error): ?>
            <div class="row mt-4">
                <?php if (empty($searchResults)): ?>
                    <div class="col-12">
                        <div class="no-results">
                            <i class="fas fa-bus fa-4x mb-3 text-muted"></i>
                            <h4>No buses available</h4>
                            <p>We couldn't find any buses for your selected route and date. Try adjusting your search criteria or selecting a different date.</p>
                            <a href="../index.php" class="btn btn-primary">Search Different Route</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($searchResults as $bus): ?>
                        <div class="col-lg-6 mb-4">
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
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="text-white py-4 mt-5" style="background-color: #800000;">
        <div class="container">
            <div class="row">
                <p>&copy; 2025 Lanka Transit. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>