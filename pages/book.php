<?php
/**
 * Bus Booking Handler
 * Processes seat booking and redirects to confirmation
 */
session_start();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: ../index.php?error=invalid_request');
    exit;
}

// Validate and sanitize input
$required_fields = ['name', 'phone', 'gender', 'seat', 'bus_id', 'travel_date', 'origin', 'destination', 'fare'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $_SESSION['error'] = "Missing or empty required field: $field";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
}

$passenger_name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$gender = strtolower(trim($_POST['gender']));
$nic = trim($_POST['nic'] ?? '');
$seat_number = (int)$_POST['seat'];
$bus_id = (int)$_POST['bus_id'];
$travel_date = $_POST['travel_date'];
$origin = trim($_POST['origin']);
$destination = trim($_POST['destination']);
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

// Validate travel date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $travel_date) || !strtotime($travel_date)) {
    $_SESSION['error'] = "Invalid travel date format.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}

// Validate origin and destination
if (strlen($origin) > 50 || strlen($destination) > 50) {
    $_SESSION['error'] = "Origin or Destination exceeds maximum length of 50 characters.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}

try {
    require_once '../classes/Database.php';
    $pdo = Database::getConnection();
    if (!$pdo) {
        $_SESSION['error'] = "Database connection failed.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
    
    // Validate bus_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Bus WHERE ID = ?");
    $stmt->execute([$bus_id]);
    if ($stmt->fetchColumn() == 0) {
        $_SESSION['error'] = "Invalid bus ID.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
    
    // Check if seat is already booked
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM Booking 
        WHERE BusID = ? AND SeatNumber = ? 
        AND TravelDate = ? AND Status = 'confirmed'
    ");
    $stmt->execute([$bus_id, $seat_number, $travel_date]);
    
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Seat $seat_number is already booked for this date.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
    
    // Check lady seat restriction
    if ($seat_number >= 1 && $seat_number <= 8 && $gender !== 'female') {
        $departure = new DateTime("$travel_date $departure_time");
        $threeHoursBefore = $departure->modify('-3 hours');
        $now = new DateTime();
        if ($now < $threeHoursBefore) {
            $_SESSION['error'] = "Seats 1-8 are reserved for ladies only.";
            header('Location: seatbooking.php?' . http_build_query($_GET));
            exit;
        }
    }
    
    // Find or create user
    $stmt = $pdo->prepare("SELECT ID FROM User WHERE PhoneNumber = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $stmt = $pdo->prepare("INSERT INTO User (Name, PhoneNumber, Email, PasswordHash, Role) VALUES (?, ?, ?, ?, 'registered user')");
        $email = $phone . '@lankatransit.com'; // Generate a dummy email if none provided
        $passwordHash = password_hash(uniqid(), PASSWORD_DEFAULT); // Generate a dummy password
        $stmt->execute([$passenger_name, $phone, $email, $passwordHash]);
        $user_id = $pdo->lastInsertId();
    } else {
        // Update name if changed
        $stmt = $pdo->prepare("UPDATE User SET Name = ? WHERE ID = ?");
        $stmt->execute([$passenger_name, $user['ID']]);
        $user_id = $user['ID'];
    }
    
    // Save booking
    $stmt = $pdo->prepare("
        INSERT INTO Booking (UserId, BusID, SeatNumber, PhoneNumber, TravelDate, Fare, Status, BookingTime, Origin, Destination)
        VALUES (?, ?, ?, ?, ?, ?, 'confirmed', NOW(), ?, ?)
    ");
    $stmt->execute([$user_id, $bus_id, $seat_number, $phone, $travel_date, $fare, $origin, $destination]);
    $booking_id = $pdo->lastInsertId();
    
    // Save gender in Booking_2
    $stmt = $pdo->prepare("
        INSERT INTO Booking_2 (booking_id, gender, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$booking_id, $gender]);
    
    // Store booking data in session
    $_SESSION['booking_data'] = [
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
        'arrival_time' => $arrival_time,
        'booking_id' => $booking_id
    ];
    
    // Redirect to confirmation page
    header('Location: confirmation.php');
    exit;
    
} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    $_SESSION['error'] = "Booking system error: " . $e->getMessage();
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}
?>