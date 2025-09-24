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
            // Use TravelDate instead of BookingTime to determine future bookings
            $sql = "SELECT 
                        b.ID as booking_id,
                        b.SeatNumber,
                        b.Fare,
                        b.BookingTime,
                        b.TravelDate,
                        b.Origin,
                        b.Destination,
                        bus.BusNumber
                    FROM Booking b
                    LEFT JOIN Bus bus ON b.BusID = bus.ID
                    WHERE b.PhoneNumber = ? 
                    AND b.Status IN ('confirmed', 'completed')
                    AND b.TravelDate > CURDATE()
                    ORDER BY b.TravelDate ASC, b.BookingTime ASC";
                    
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
                    'error' => 'Your account information is incomplete. Please contact support.'
                ];
            }
            
            // Verify the booking belongs to the user AND is a future booking
            $sql = "SELECT ID, BookingTime, TravelDate FROM Booking 
                    WHERE ID = ? AND PhoneNumber = ? AND Status IN ('confirmed', 'completed') AND TravelDate > CURDATE()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$bookingId, $user['PhoneNumber']]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                error_log("Security: User $userId attempted to cancel unauthorized/past booking $bookingId");
                return [
                    'success' => false,
                    'error' => 'Please select one of your upcoming trips. Only future bookings can be cancelled.'
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
                    'message' => 'Your cancellation request has been submitted successfully! We will process your request and notify you soon.'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Unable to submit your cancellation request at the moment. Please try again.'
                ];
            }
        } catch (Exception $e) {
            error_log("Error submitting cancellation: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Something went wrong while processing your request. Please try again later.'
            ];
        }
    }
    
    /**
     * Get user's cancellation history
     * @param int $userId
     * @return array
     */
    public function getUserCancellationHistory($userId) {
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
            error_log("DEBUG: Looking for cancellations for phone: $userPhoneNumber, userId: $userId");
            
            // Query to get user's cancellation history with booking details
            // Simplified query to avoid column existence check
            $sql = "SELECT 
                        bc.ID as cancellation_id,
                        bc.BookingID,
                        bc.CancellationReason,
                        bc.RequestedAt,
                        bc.Status as cancellation_status,
                        bc.ProcessedAt,
                        '' as AdminNotes,
                        b.SeatNumber,
                        b.Fare,
                        b.BookingTime,
                        bus.BusNumber,
                        r.Origin,
                        r.Destination
                    FROM BookingCancellation bc
                    JOIN Booking b ON bc.BookingID = b.ID
                    LEFT JOIN Bus bus ON b.BusID = bus.ID
                    LEFT JOIN Route r ON bus.RouteId = r.ID
                    WHERE bc.UserID = ? AND b.PhoneNumber = ?
                    ORDER BY bc.RequestedAt DESC";
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $userPhoneNumber]);
            $cancellations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log for debugging
            error_log("DEBUG: Found " . count($cancellations) . " cancellation records for user " . $userId);
            error_log("DEBUG: Query executed with userId: $userId, phone: $userPhoneNumber");
            
            if (empty($cancellations)) {
                // Let's check if there are any cancellations at all for this user
                $debugSql = "SELECT COUNT(*) as count FROM BookingCancellation WHERE UserID = ?";
                $debugStmt = $this->pdo->prepare($debugSql);
                $debugStmt->execute([$userId]);
                $debugResult = $debugStmt->fetch(PDO::FETCH_ASSOC);
                error_log("DEBUG: Total cancellations for userId $userId: " . $debugResult['count']);
                
                // Check if there are any bookings for this phone number
                $debugSql2 = "SELECT COUNT(*) as count FROM Booking WHERE PhoneNumber = ?";
                $debugStmt2 = $this->pdo->prepare($debugSql2);
                $debugStmt2->execute([$userPhoneNumber]);
                $debugResult2 = $debugStmt2->fetch(PDO::FETCH_ASSOC);
                error_log("DEBUG: Total bookings for phone $userPhoneNumber: " . $debugResult2['count']);
            }
            
            return $cancellations;
        } catch (Exception $e) {
            error_log("Error fetching cancellation history: " . $e->getMessage());
            return [];
        }
    }
}
?>