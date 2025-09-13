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
     * Get user's upcoming bookings
     * @param int $userId
     * @return array
     */
    public function getUserBookings($userId) {
        try {
            // Simple query to get user's confirmed bookings
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
                    WHERE b.UserId = ? 
                    AND b.Status = 'confirmed'
                    ORDER BY b.BookingTime DESC";
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching bookings: " . $e->getMessage());
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
            // Insert into BookingCancellation table
            $sql = "INSERT INTO BookingCancellation (BookingID, UserID, CancellationReason, RequestedAt, Status) 
                    VALUES (?, ?, ?, NOW(), 'pending')";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$bookingId, $userId, $reason]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cancellation request submitted successfully!'
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
