<?php
/**
 * Bus Booking Handler
 * Processes seat booking with pending status and requires confirmation
 */
session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Bus.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: ../index.php?error=invalid_request');
    exit;
}

// Initialize database and classes
$database = new Database();
$pdo = $database->getConnection();
if (!$pdo) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}

$booking = new Booking($pdo);
$bus = new Bus($pdo);

// Validate and sanitize input
$required_fields = ['name', 'phone', 'gender', 'seat', 'bus_id', 'travel_date', 'origin', 'destination', 'fare'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $_SESSION['error'] = "Missing or empty required field: $field";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }
}

// Prepare booking data
$booking_data = [
    'passenger_name' => trim($_POST['name']),
    'phone' => trim($_POST['phone']),
    'gender' => strtolower(trim($_POST['gender'])),
    'nic' => trim($_POST['nic'] ?? ''),
    'seat_number' => (string)$_POST['seat'],
    'bus_id' => (int)$_POST['bus_id'],
    'travel_date' => $_POST['travel_date'],
    'origin' => trim($_POST['origin']),
    'destination' => trim($_POST['destination']),
    'fare' => (float)$_POST['fare'],
    'bus_number' => $_POST['bus_number'] ?? '',
    'departure_time' => $_POST['departure_time'] ?? '',
    'arrival_time' => $_POST['arrival_time'] ?? ''
];

try {
    // Validate gender
    if (!in_array($booking_data['gender'], ['male', 'female', 'undisclosed'])) {
        $_SESSION['error'] = "Invalid gender selection.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }

    // Validate NIC if provided
    if (!empty($booking_data['nic']) && !$booking->isValidNIC($booking_data['nic'])) {
        $_SESSION['error'] = "Invalid Sri Lankan NIC format.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }

    // Validate travel date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $booking_data['travel_date']) || !strtotime($booking_data['travel_date'])) {
        $_SESSION['error'] = "Invalid travel date format.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }

    // Validate origin and destination length
    if (strlen($booking_data['origin']) > 50 || strlen($booking_data['destination']) > 50) {
        $_SESSION['error'] = "Origin or Destination exceeds maximum length of 50 characters.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }

    // Validate bus existence using Bus class
    $bus_details = $bus->getBusDetails($booking_data['bus_id']);
    if (isset($bus_details['error']) || !$bus_details['success']) {
        $_SESSION['error'] = "Invalid bus ID.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }

    // Check seat availability using Booking class
    if (!$booking->checkSeatAvailability($booking_data['bus_id'], $booking_data['seat_number'], $booking_data['travel_date'])) {
        $_SESSION['error'] = "Seat {$booking_data['seat_number']} is already booked for this date.";
        header('Location: seatbooking.php?' . http_build_query($_GET));
        exit;
    }

    // Check lady seat restriction using Bus and Booking classes
    if ($bus->isLadySeat($booking_data['bus_id'], $booking_data['seat_number']) && $booking_data['gender'] !== 'female') {
        $departure = new DateTime("{$booking_data['travel_date']} {$booking_data['departure_time']}");
        $threeHoursBefore = $departure->modify('-3 hours');
        $now = new DateTime();
        if ($now < $threeHoursBefore) {
            $_SESSION['error'] = "Seats 1-8 are reserved for ladies only.";
            header('Location: seatbooking.php?' . http_build_query($_GET));
            exit;
        }
    }

    // Start transaction
    $pdo->beginTransaction();

    // Create or get user
    $user_id = $booking->createOrGetUser($booking_data);
    if (!$user_id) {
        throw new Exception("Failed to create or retrieve user");
    }

    // Create booking with pending status
    $stmt = $pdo->prepare("
        INSERT INTO Booking (UserId, BusID, SeatNumber, PhoneNumber, Fare, Status, BookingTime, Origin, Destination)
        VALUES (?, ?, ?, ?, ?, 'pending', NOW(), ?, ?)
    ");
    $result = $stmt->execute([
        $user_id,
        $booking_data['bus_id'],
        $booking_data['seat_number'],
        $booking_data['phone'],
        $booking_data['fare'],
        $booking_data['origin'],
        $booking_data['destination']
    ]);

    if (!$result) {
        throw new Exception("Failed to save booking");
    }

    $booking_id = $pdo->lastInsertId();

    // Create gender record
    if (!empty($booking_data['gender'])) {
        if (!$booking->createGenderRecord($booking_id, $booking_data['gender'])) {
            throw new Exception("Failed to save gender record");
        }
    }

    // Update seat status to booked
    if (!$booking->updateSeatStatus($booking_data['bus_id'], $booking_data['seat_number'], 'booked')) {
        throw new Exception("Failed to update seat status");
    }

    // Commit transaction
    $pdo->commit();

    // Generate booking reference
    $booking_reference = 'LT-' . str_pad($booking_id, 6, '0', STR_PAD_LEFT);

    // Placeholder for confirmation step (e.g., payment processing)
    // Assuming payment or confirmation happens here
    // For now, we'll simulate a successful confirmation
    // In a real scenario, this would involve payment gateway integration
    $confirmation_success = true; // Replace with actual confirmation logic (e.g., payment API call)

    if ($confirmation_success) {
        // Confirm the booking
        if (!$booking->updateBookingStatus($booking_id, 'confirmed')) {
            throw new Exception("Failed to confirm booking");
        }

        // Store booking data in session
        $_SESSION['booking_data'] = [
            'passenger_name' => $booking_data['passenger_name'],
            'phone' => $booking_data['phone'],
            'gender' => $booking_data['gender'],
            'nic' => $booking_data['nic'],
            'origin' => $booking_data['origin'],
            'destination' => $booking_data['destination'],
            'travel_date' => $booking_data['travel_date'],
            'bus_id' => $booking_data['bus_id'],
            'bus_number' => $booking_data['bus_number'],
            'seat_number' => $booking_data['seat_number'],
            'fare' => $booking_data['fare'],
            'departure_time' => $booking_data['departure_time'],
            'arrival_time' => $booking_data['arrival_time'],
            'booking_id' => $booking_id,
            'booking_reference' => $booking_reference
        ];

        // Redirect to confirmation page
        header('Location: confirmation.php');
        exit;
    } else {
        // If confirmation fails, cancel the booking
        $booking->updateBookingStatus($booking_id, 'cancelled');
        $booking->updateSeatStatus($booking_data['bus_id'], $booking_data['seat_number'], 'available');
        throw new Exception("Booking confirmation failed.");
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Booking error: " . $e->getMessage());
    $_SESSION['error'] = "Booking system error: " . $e->getMessage();
    header('Location: seatbooking.php?' . http_build_query($_GET));
    exit;
}
?>
