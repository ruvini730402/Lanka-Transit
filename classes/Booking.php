<?php
/**
 * Booking Class for Lanka Transit
 * Handles all booking-related operations following OOP principles
 */

require_once __DIR__ . '/Database.php';

class Booking {
    private $id;
    private $userId;
    private $busId;
    private $seatNumber;
    private $bookingTime;
    private $status;
    private $phoneNumber;
    private $fare;
    private $gender;
    private $createdAt;
    private $updatedAt;
    private $pdo;
    
    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $database = new Database();
            $this->pdo = $database->getConnection();
        }
    }
    
    /**
     * Create a new booking record
     * @param array $data Booking data
     * @return array Result with success status and booking details
     */
    public function createBooking($data) {
        try {
            // Validate booking data
            $validation = $this->validateBookingData($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => $validation['error']
                ];
            }
            
            // Check seat availability
            if (!$this->checkSeatAvailability($data['bus_id'], $data['seat_number'], $data['travel_date'])) {
                return [
                    'success' => false,
                    'error' => 'Selected seat is not available'
                ];
            }
            
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Create user if needed
            $userId = $this->createOrGetUser($data);
            if (!$userId) {
                throw new Exception("Failed to create or retrieve user");
            }
            
            // Create booking record
            $bookingId = $this->saveBooking($userId, $data);
            if (!$bookingId) {
                throw new Exception("Failed to save booking");
            }
            
            // Create gender record if gender is provided
            if (!empty($data['gender'])) {
                $this->createGenderRecord($bookingId, $data['gender']);
            }
            
            // Update seat status
            $this->updateSeatStatus($data['bus_id'], $data['seat_number'], 'booked');
            
            // Commit transaction
            $this->pdo->commit();
            
            // Generate booking reference
            $bookingReference = 'LT-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT);
            
            return [
                'success' => true,
                'booking_id' => $bookingId,
                'booking_reference' => $bookingReference,
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollback();
            }
            error_log("Booking creation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get bookings by user ID
     * @param int $userId
     * @return array
     */
    public function getBookingsByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, bus.BusNumber, r.Origin, r.Destination, p.Status as PaymentStatus
            FROM Booking b
            LEFT JOIN Bus bus ON b.BusID = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Payment p ON b.ID = p.BookingId
            WHERE b.UserId = ?
            ORDER BY b.BookingTime DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update booking status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateBookingStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE Booking SET Status = ? WHERE ID = ?");
        return $stmt->execute([$status, $id]);
    }
    
    /**
     * Check seat availability
     * @param int $busId
     * @param string $seatNumber
     * @param string $date
     * @return bool
     */
    public function checkSeatAvailability($busId, $seatNumber, $date) {
        // Check if seat is already booked for this date
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM Booking b
            JOIN Schedule s ON b.BusID = s.BusID
            WHERE b.BusID = ? AND b.SeatNumber = ? 
            AND DATE(s.DepartureTime) = ? AND b.Status = 'confirmed'
        ");
        $stmt->execute([$busId, $seatNumber, $date]);
        $bookedCount = $stmt->fetchColumn();
        
        // Also check seat table if it exists
        try {
            $stmt = $this->pdo->prepare("
                SELECT Status FROM Seat 
                WHERE BusID = ? AND SeatNumber = ?
            ");
            $stmt->execute([$busId, $seatNumber]);
            $seatStatus = $stmt->fetchColumn();
            
            if ($seatStatus === 'booked') {
                return false;
            }
        } catch (PDOException $e) {
            // Seat table might not exist, continue with booking count check
        }
        
        return $bookedCount == 0;
    }
    
    /**
     * Validate booking data
     * @param array $data
     * @return array
     */
    public function validateBookingData($data) {
        $required = ['passenger_name', 'phone', 'bus_id', 'seat_number', 'fare'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'valid' => false,
                    'error' => "Missing required field: $field"
                ];
            }
        }
        
        // Validate phone number
        if (!$this->validateInput($data['phone'], 'phone')) {
            return [
                'valid' => false,
                'error' => 'Invalid phone number format'
            ];
        }
        
        // Validate fare
        if (!is_numeric($data['fare']) || $data['fare'] <= 0) {
            return [
                'valid' => false,
                'error' => 'Invalid fare amount'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Process booking (legacy method for compatibility)
     */
    public function processBooking() {
        // This method can be used for additional processing if needed
        return true;
    }
    
    /**
     * Validate input based on type
     * @param mixed $data
     * @param string $type
     * @return bool
     */
    public function validateInput($data, $type) {
        switch ($type) {
            case 'phone':
                return preg_match('/^0[0-9]{9}$/', $data);
            case 'email':
                return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
            case 'nic':
                return $this->isValidNIC($data);
            default:
                return !empty($data);
        }
    }
    
    /**
     * Check if seat is booked
     * @param int $busId
     * @param string $seatNumber
     * @param string $date
     * @return bool
     */
    public function isSeatBooked($busId, $seatNumber, $date = null) {
        return !$this->checkSeatAvailability($busId, $seatNumber, $date ?? date('Y-m-d'));
    }
    
    /**
     * Check if lady seat has restrictions
     * @param int $busId
     * @param string $seatNumber
     * @param string $gender
     * @return bool
     */
    public function isLadySeatRestricted($busId, $seatNumber, $gender) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT IsLadySeat FROM Seat 
                WHERE BusID = ? AND SeatNumber = ?
            ");
            $stmt->execute([$busId, $seatNumber]);
            $isLadySeat = $stmt->fetchColumn();
            
            // If it's a lady seat and passenger is not female, restrict
            return ($isLadySeat && $gender !== 'female');
        } catch (PDOException $e) {
            // Seat table might not exist
            return false;
        }
    }
    
    /**
     * Save booking to database
     * @param int $userId
     * @param array $data
     * @return int|false
     */
    public function saveBooking($userId, $data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO Booking (UserId, BusID, SeatNumber, PhoneNumber, Fare, Status) 
            VALUES (?, ?, ?, ?, ?, 'confirmed')
        ");
        
        $busId = $data['bus_id'] ?? $data['BusID'] ?? null;
        
        $result = $stmt->execute([
            $userId,
            $busId,
            $data['seat_number'],
            $data['phone'],
            $data['fare']
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }
    
    /**
     * Validate NIC number
     * @param string $nic
     * @return bool
     */
    public function isValidNIC($nic) {
        // Sri Lankan NIC validation
        $pattern1 = '/^[0-9]{9}[vVxX]$/'; // Old format
        $pattern2 = '/^[0-9]{12}$/';       // New format
        
        return preg_match($pattern1, $nic) || preg_match($pattern2, $nic);
    }
    
    /**
     * Get total bookings count
     * @return int
     */
    public function getTotalBookings() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Booking");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Create gender record for booking
     * @param int $bookingId
     * @param string $gender
     * @return bool
     */
    public function createGenderRecord($bookingId, $gender) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO Booking_2 (booking_id, gender) 
                VALUES (?, ?)
            ");
            return $stmt->execute([$bookingId, $gender]);
        } catch (PDOException $e) {
            error_log("Gender record creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get gender by booking ID
     * @param int $bookingId
     * @return string|null
     */
    public function getGenderByBooking($bookingId) {
        try {
            $stmt = $this->pdo->prepare("SELECT gender FROM Booking_2 WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            return $stmt->fetchColumn() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Update gender for booking
     * @param int $bookingId
     * @param string $gender
     * @return bool
     */
    public function updateGender($bookingId, $gender) {
        try {
            $stmt = $this->pdo->prepare("UPDATE Booking_2 SET gender = ? WHERE booking_id = ?");
            return $stmt->execute([$gender, $bookingId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Create or get existing user
     * @param array $data
     * @return int|false
     */
    private function createOrGetUser($data) {
        // Generate email if not provided
        $email = $data['email'] ?? $this->generateEmailFromName($data['passenger_name']);
        
        // Check if user exists
        $stmt = $this->pdo->prepare("SELECT ID FROM User WHERE Email = ?");
        $stmt->execute([$email]);
        $userId = $stmt->fetchColumn();
        
        if ($userId) {
            return $userId;
        }
        
        // Create new user
        $stmt = $this->pdo->prepare("
            INSERT INTO User (Name, Email, PasswordHash, PhoneNumber, Role) 
            VALUES (?, ?, ?, ?, 'guest user')
        ");
        
        $result = $stmt->execute([
            $data['passenger_name'],
            $email,
            password_hash('demo123', PASSWORD_DEFAULT),
            $data['phone']
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }
    
    /**
     * Generate email from passenger name
     * @param string $name
     * @return string
     */
    private function generateEmailFromName($name) {
        $baseEmail = strtolower(str_replace(' ', '', $name)) . '@demo.com';
        $email = $baseEmail;
        $counter = 1;
        
        // Ensure email is unique
        $stmt = $this->pdo->prepare("SELECT ID FROM User WHERE Email = ?");
        $stmt->execute([$email]);
        
        while ($stmt->fetch()) {
            $email = str_replace('@demo.com', $counter . '@demo.com', $baseEmail);
            $counter++;
            $stmt->execute([$email]);
        }
        
        return $email;
    }
    
    /**
     * Update seat status
     * @param int $busId
     * @param string $seatNumber
     * @param string $status
     * @return bool
     */
    private function updateSeatStatus($busId, $seatNumber, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE Seat SET Status = ? 
                WHERE BusID = ? AND SeatNumber = ?
            ");
            return $stmt->execute([$status, $busId, $seatNumber]);
        } catch (PDOException $e) {
            // Seat table might not exist
            error_log("Seat status update failed: " . $e->getMessage());
            return true; // Continue without error if seat table doesn't exist
        }
    }
    
    /**
     * Get booking details by ID
     * @param int $bookingId
     * @return array|null
     */
    public function getBookingById($bookingId) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, u.Name as PassengerName, u.Email, 
                   bus.BusNumber, r.Origin, r.Destination,
                   p.Status as PaymentStatus, p.TransactionId
            FROM Booking b
            JOIN User u ON b.UserId = u.ID
            LEFT JOIN Bus bus ON b.BusID = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Payment p ON b.ID = p.BookingId
            WHERE b.ID = ?
        ");
        $stmt->execute([$bookingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
?>
