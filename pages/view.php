<?php
/**
 * View Booking & Get Seat Availability
 * Handles both booking lookup and seat availability API
 */

// Handle seat availability API request
if (isset($_GET['api']) && $_GET['api'] === 'seats') {
    header('Content-Type: application/json');
    
    require_once '../classes/Database.php';
    require_once '../classes/Bus.php';
    
    if (!isset($_GET['bus_id']) || !isset($_GET['date'])) {
        echo json_encode(['error' => 'Missing bus_id or date parameter']);
        exit;
    }
    
    $bus_id = (int)$_GET['bus_id'];
    $travel_date = $_GET['date'];
    $model = $_GET['model'] ?? '49'; // Default to 49 if not provided
    $total_seats = ($model === '54') ? 54 : 49;
    
    try {
        $pdo = Database::getConnection();
        
        // Query only booked seats
        $stmt = $pdo->prepare("
            SELECT b.SeatNumber as seat_number, LOWER(u.Gender) as gender_preference
            FROM Booking b
            LEFT JOIN User u ON b.UserId = u.ID
            WHERE b.BusID = ? AND b.TravelDate = ? AND b.Status = 'booked'
        ");
        $stmt->execute([$bus_id, $travel_date]);
        $bookedSeats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate full seat data
        $seatData = [];
        for ($i = 1; $i <= $total_seats; $i++) {
            $status = 'available';
            $gender_pref = null;
            $is_lady_seat = ($i <= 8) ? true : false;
            
            foreach ($bookedSeats as $booked) {
                if ($booked['seat_number'] == $i) {
                    $status = 'booked';
                    $gender_pref = $booked['gender_preference'];
                    break;
                }
            }
            
            $seatData[] = [
                'seat' => $i,
                'status' => $status,
                'gender_preference' => $gender_pref,
                'is_lady_seat' => $is_lady_seat
            ];
        }
        
        echo json_encode(['seats' => $seatData]);
        
    } catch (Exception $e) {
        error_log("Seat availability error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to load seat availability']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-form {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .booking-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">Home</a>
                <a class="nav-link" href="seatbooking.php">Book Ticket</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="search-form">
                    <h3 class="mb-3">
                        <i class="fas fa-search me-2"></i>Find Your Booking
                    </h3>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="identifier" class="form-control form-control-lg" 
                                       placeholder="Enter Booking ID or Phone Number" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-light btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    require_once __DIR__ . '/../classes/Database.php';

                    $id = trim($_POST['identifier']);

                    try {
                        $pdo = Database::getConnection();

                        if ($pdo === null) {
                            echo '<div class="alert alert-danger">Database connection failed.</div>';
                        } else {
                            // Updated query to match ER.txt schema
                            $stmt = $pdo->prepare("
                                SELECT b.*, u.Name, u.Email, u.PhoneNumber, 
                                       bus.BusNumber, r.Origin, r.Destination,
                                       p.TransactionId, p.Amount
                                FROM Booking b
                                LEFT JOIN User u ON b.UserId = u.ID
                                LEFT JOIN Bus bus ON b.BusID = bus.ID
                                LEFT JOIN Route r ON bus.RouteId = r.ID
                                LEFT JOIN Payment p ON b.ID = p.BookingId
                                WHERE b.ID = ? OR u.PhoneNumber = ?
                                ORDER BY b.BookingTime DESC
                            ");
                            $stmt->execute([$id, $id]);
                            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($results) {
                                echo '<div class="row">';
                                foreach ($results as $row) {
                                    echo '<div class="col-md-6 mb-4">
                                        <div class="card booking-card">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-ticket-alt me-2"></i>
                                                    Booking ID: ' . htmlspecialchars($row['ID']) . '
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <strong>Passenger:</strong><br>
                                                        ' . htmlspecialchars($row['Name']) . '
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Phone:</strong><br>
                                                        ' . htmlspecialchars($row['PhoneNumber']) . '
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <strong>Route:</strong><br>
                                                        ' . htmlspecialchars($row['Origin'] ?? 'N/A') . ' → ' . htmlspecialchars($row['Destination'] ?? 'N/A') . '
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Bus:</strong><br>
                                                        ' . htmlspecialchars($row['BusNumber'] ?? 'N/A') . '
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <strong>Seat:</strong><br>
                                                        <span class="badge bg-info fs-6">' . htmlspecialchars($row['SeatNumber']) . '</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Status:</strong><br>
                                                        <span class="badge bg-success fs-6">' . htmlspecialchars($row['Status']) . '</span>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <strong>Fare:</strong><br>
                                                        Rs. ' . number_format($row['Fare'], 2) . '
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Booked:</strong><br>
                                                        ' . date('M j, Y g:i A', strtotime($row['BookingTime'])) . '
                                                    </div>
                                                </div>
                                                ' . ($row['TransactionId'] ? '
                                                <hr>
                                                <small class="text-muted">
                                                    <strong>Transaction ID:</strong> ' . htmlspecialchars($row['TransactionId']) . '
                                                </small>' : '') . '
                                            </div>
                                        </div>
                                    </div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No booking found with that ID or phone number.
                                </div>';
                            }
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error: ' . htmlspecialchars($e->getMessage()) . '
                        </div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>