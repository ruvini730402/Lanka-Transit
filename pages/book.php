<?php
/**
 * Bus Booking Handler
 * Processes seat booking and redirects to confirmation
 */
session_start();

// Check if this is an AJAX request (old booking class functionality)
if (isset($_POST['seat']) && !isset($_POST['bus_id'])) {
    header('Content-Type: application/json');
    require_once 'booking.php';  // Include the OOP class
    
    // Create and process the booking using POST data
    $booking = new Booking($_POST);
    $booking->processBooking();
    exit;
}

// New booking process
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Validate and sanitize input
$required_fields = ['name', 'phone', 'gender', 'seat', 'bus_id', 'travel_date', 'origin', 'destination', 'fare'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Missing required field: $field";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
}

$passenger_name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$gender = $_POST['gender'];
$nic = trim($_POST['nic'] ?? '');
$seat_number = $_POST['seat'];
$bus_id = $_POST['bus_id'];
$travel_date = $_POST['travel_date'];
$origin = $_POST['origin'];
$destination = $_POST['destination'];
$fare = (float)$_POST['fare'];
$bus_number = $_POST['bus_number'] ?? '';
$departure_time = $_POST['departure_time'] ?? '';
$arrival_time = $_POST['arrival_time'] ?? '';

// Validate phone number
if (!preg_match('/^\d{10}$/', $phone)) {
    $_SESSION['error'] = "Phone number must be exactly 10 digits.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}

// Validate gender
if (!in_array($gender, ['male', 'female', 'undisclosed'])) {
    $_SESSION['error'] = "Invalid gender selection.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}

// Validate NIC if provided
if (!empty($nic) && !preg_match('/^(\d{9}[vVxX]|\d{12})$/', $nic)) {
    $_SESSION['error'] = "Invalid Sri Lankan NIC format.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}

try {
    require_once '../classes/Database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Check if seat is already booked for this date
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM Booking 
        WHERE BusID = ? AND SeatNumber = ? 
        AND DATE(BookingTime) = ? AND Status = 'confirmed'
    ");
    $stmt->execute([$bus_id, $seat_number, $travel_date]);
    
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Seat $seat_number is already booked for this date.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
    
    // Check lady seat restriction (seats 1-8 for ladies only)
    $seat_num = (int)$seat_number;
    if ($seat_num >= 1 && $seat_num <= 8 && $gender !== 'female') {
        $_SESSION['error'] = "Seats 1-8 are reserved for ladies only.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
    
    // All validations passed - redirect to confirmation page with booking data
    $booking_data = [
        'passenger_name' => $passenger_name,
        'phone' => $phone,
        'gender' => $gender,
        'nic' => $nic,
        'origin' => $origin,
        'destination' => $destination,
        'travel_date' => $travel_date,
        'bus_id' => $bus_id,
        'bus_number' => $bus_number,
        'seat_number' => $seat_number,
        'fare' => $fare,
        'departure_time' => $departure_time,
        'arrival_time' => $arrival_time
    ];
    
    // Store booking data in session
    $_SESSION['booking_data'] = $booking_data;
    
    // Redirect to payment page instead of confirmation
    header('Location: payment.php');
    exit;
    
} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    $_SESSION['error'] = "Booking system error. Please try again.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}
?>
