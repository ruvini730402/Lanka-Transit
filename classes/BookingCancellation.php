<?php
/**
 * Simple BookingCancellation Class
 */

require_once __DIR__ . '/Database.php';

class BookingCancellation {
    private $pdo;
    
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }
    
    /**
     * Get user's upcoming bookings (future bookings only - cancellation only allowed for future trips)
     * @param int $userId
     * @return array
     */
    public function getUserBookings($userId) {
        try {
            // Get user's phone number first
            $sql = "SELECT PhoneNumber FROM User WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !$user['PhoneNumber']) {
                error_log("User phone number not found for userId: $userId");
                return [];
            }
            
            $userPhoneNumber = $user['PhoneNumber'];
            
            // Query to get user's future confirmed bookings using phone number
            $sql = "SELECT 
                        b.ID as booking_id,
                        b.SeatNumber,
                        b.Fare,
                        b.BookingTime,
                        bus.BusNumber,
                        r.Origin,
                        r.Destination
                    FROM Booking b
                    LEFT JOIN Bus bus ON b.BusID = bus.ID
                    LEFT JOIN Route r ON bus.RouteId = r.ID
                    WHERE b.PhoneNumber = ? 
                    AND b.Status = 'confirmed'
                    AND b.BookingTime > NOW()
                    ORDER BY b.BookingTime ASC";
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userPhoneNumber]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log for debugging
            error_log("DEBUG: Found " . count($bookings) . " FUTURE bookings for cancellation, phone " . $userPhoneNumber);
            
            return $bookings;
        } catch (Exception $e) {
            error_log("Error fetching bookings for cancellation: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Submit cancellation request
     * @param int $bookingId
     * @param int $userId
     * @param string $reason
     * @return array
     */
    public function submitCancellation($bookingId, $userId, $reason) {
        try {
            // First, get user's phone number
            $sql = "SELECT PhoneNumber FROM User WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !$user['PhoneNumber']) {
                return [
                    'success' => false,
                    'error' => 'User phone number not found'
                ];
            }
            
            // Verify the booking belongs to the user AND is a future booking
            $sql = "SELECT ID, BookingTime FROM Booking 
                    WHERE ID = ? AND PhoneNumber = ? AND Status = 'confirmed' AND BookingTime > NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$bookingId, $user['PhoneNumber']]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                error_log("Security: User $userId attempted to cancel unauthorized/past booking $bookingId");
                return [
                    'success' => false,
                    'error' => 'Invalid booking selection. You can only cancel future bookings that belong to you.'
                ];
            }
            
            // Insert into BookingCancellation table
            $sql = "INSERT INTO BookingCancellation (BookingID, UserID, CancellationReason, RequestedAt, Status) 
                    VALUES (?, ?, ?, NOW(), 'pending')";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$bookingId, $userId, $reason]);
            
            if ($result) {
                error_log("Cancellation request submitted successfully - BookingID: $bookingId, UserID: $userId");
                return [
                    'success' => true,
                    'message' => 'Cancellation request submitted successfully! We will process your request within 24 hours.'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to submit cancellation request'
                ];
            }
        } catch (Exception $e) {
            error_log("Error submitting cancellation: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while processing your request'
            ];
        }
    }
}
?>
