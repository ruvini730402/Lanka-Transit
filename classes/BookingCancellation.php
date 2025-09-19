<?php
/**
 * BookingCancellation Class
 * Handles booking cancellation requests and processing for Lanka Transit
 */

require_once 'Database.php';

class BookingCancellation {
    private $id;
    private $bookingId;
    private $userId;
    private $cancellationReason;
    private $requestedAt;
    private $status;
    private $processedBy;
    private $processedAt;
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
     * Create a new cancellation request
     * @param int $bookingId
     * @param int $userId
     * @param string $reason
     * @return array Result with success status and cancellation ID
     */
    public function createCancellationRequest($bookingId, $userId, $reason) {
        try {
            // Validate inputs
            if (empty($bookingId) || empty($reason)) {
                return [
                    'success' => false,
                    'error' => 'Booking ID and cancellation reason are required'
                ];
            }
            
            // Check if booking exists and belongs to user (if userId provided)
            $bookingCheck = $this->validateBookingOwnership($bookingId, $userId);
            if (!$bookingCheck['valid']) {
                return [
                    'success' => false,
                    'error' => $bookingCheck['error']
                ];
            }
            
            // Check if booking is eligible for cancellation
            $eligibilityCheck = $this->checkCancellationEligibility($bookingId);
            if (!$eligibilityCheck['eligible']) {
                return [
                    'success' => false,
                    'error' => $eligibilityCheck['error']
                ];
            }
            
            // Check if there's already a pending cancellation request
            $existingRequest = $this->getPendingCancellationByBooking($bookingId);
            if ($existingRequest) {
                return [
                    'success' => false,
                    'error' => 'A cancellation request is already pending for this booking'
                ];
            }
            
            // Create cancellation request
            $stmt = $this->pdo->prepare("
                INSERT INTO BookingCancellation (BookingID, UserID, CancellationReason, RequestedAt, Status) 
                VALUES (?, ?, ?, NOW(), 'pending')
            ");
            
            $result = $stmt->execute([$bookingId, $userId, $reason]);
            
            if ($result) {
                $cancellationId = $this->pdo->lastInsertId();
                return [
                    'success' => true,
                    'cancellation_id' => $cancellationId,
                    'message' => 'Cancellation request submitted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to create cancellation request'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Cancellation request creation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Database error occurred while processing your request'
            ];
        }
    }
    
    /**
     * Get cancellation requests by user ID
     * @param int $userId
     * @return array
     */
    public function getCancellationsByUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT bc.*, b.SeatNumber, b.Fare, b.BookingTime,
                       bus.BusNumber, r.Origin, r.Destination,
                       admin.Name as ProcessedByName
                FROM BookingCancellation bc
                JOIN Booking b ON bc.BookingID = b.ID
                LEFT JOIN Bus bus ON b.BusID = bus.ID
                LEFT JOIN Route r ON bus.RouteId = r.ID
                LEFT JOIN Admin admin ON bc.ProcessedBy = admin.ID
                WHERE bc.UserID = ?
                ORDER BY bc.RequestedAt DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get cancellations by user error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get cancellation requests by status
     * @param string $status
     * @return array
     */
    public function getCancellationsByStatus($status) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT bc.*, b.SeatNumber, b.Fare, b.BookingTime,
                       u.Name as UserName, u.Email, u.PhoneNumber,
                       bus.BusNumber, r.Origin, r.Destination,
                       admin.Name as ProcessedByName
                FROM BookingCancellation bc
                JOIN Booking b ON bc.BookingID = b.ID
                LEFT JOIN User u ON bc.UserID = u.ID
                LEFT JOIN Bus bus ON b.BusID = bus.ID
                LEFT JOIN Route r ON bus.RouteId = r.ID
                LEFT JOIN Admin admin ON bc.ProcessedBy = admin.ID
                WHERE bc.Status = ?
                ORDER BY bc.RequestedAt ASC
            ");
            $stmt->execute([$status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get cancellations by status error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Process a cancellation request (admin action)
     * @param int $id Cancellation ID
     * @param int $adminId Admin processing the request
     * @param string $status New status ('refunded' or 'declined')
     * @return array
     */
    public function processCancellation($id, $adminId, $status) {
        try {
            // Validate status
            if (!in_array($status, ['refunded', 'declined'])) {
                return [
                    'success' => false,
                    'error' => 'Invalid status. Must be "refunded" or "declined"'
                ];
            }
            
            // Get cancellation details
            $cancellation = $this->getCancellationDetails($id);
            if (!$cancellation) {
                return [
                    'success' => false,
                    'error' => 'Cancellation request not found'
                ];
            }
            
            if ($cancellation['Status'] !== 'pending') {
                return [
                    'success' => false,
                    'error' => 'This cancellation request has already been processed'
                ];
            }
            
            $this->pdo->beginTransaction();
            
            try {
                // Update cancellation status
                $stmt = $this->pdo->prepare("
                    UPDATE BookingCancellation 
                    SET Status = ?, ProcessedBy = ?, ProcessedAt = NOW() 
                    WHERE ID = ?
                ");
                $stmt->execute([$status, $adminId, $id]);
                
                // If refunded, update booking status to cancelled
                if ($status === 'refunded') {
                    $this->updateBookingStatus($cancellation['BookingID'], 'cancelled');
                    
                    // Update seat status to available
                    $this->updateSeatStatus($cancellation['BusID'], $cancellation['SeatNumber'], 'available');
                    
                    // Update payment status to refunded (if payment exists)
                    $this->updatePaymentStatus($cancellation['BookingID'], 'refunded');
                }
                
                $this->pdo->commit();
                
                return [
                    'success' => true,
                    'message' => "Cancellation request has been {$status} successfully"
                ];
                
            } catch (Exception $e) {
                $this->pdo->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Process cancellation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Database error occurred while processing the cancellation'
            ];
        }
    }
    
    /**
     * Get cancellation details by ID
     * @param int $id
     * @return array|null
     */
    public function getCancellationDetails($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT bc.*, b.SeatNumber, b.Fare, b.BookingTime, b.BusID, b.Status as BookingStatus,
                       u.Name as UserName, u.Email, u.PhoneNumber,
                       bus.BusNumber, r.Origin, r.Destination,
                       admin.Name as ProcessedByName,
                       p.Status as PaymentStatus, p.Amount as PaymentAmount
                FROM BookingCancellation bc
                JOIN Booking b ON bc.BookingID = b.ID
                LEFT JOIN User u ON bc.UserID = u.ID
                LEFT JOIN Bus bus ON b.BusID = bus.ID
                LEFT JOIN Route r ON bus.RouteId = r.ID
                LEFT JOIN Admin admin ON bc.ProcessedBy = admin.ID
                LEFT JOIN Payment p ON b.ID = p.BookingId
                WHERE bc.ID = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log("Get cancellation details error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all pending cancellation requests
     * @return array
     */
    public function getAllPendingCancellations() {
        return $this->getCancellationsByStatus('pending');
    }
    
    /**
     * Get cancellation statistics
     * @return array
     */
    public function getCancellationStats() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN Status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN Status = 'refunded' THEN 1 ELSE 0 END) as refunded_count,
                    SUM(CASE WHEN Status = 'declined' THEN 1 ELSE 0 END) as declined_count,
                    AVG(CASE WHEN ProcessedAt IS NOT NULL THEN 
                        TIMESTAMPDIFF(HOUR, RequestedAt, ProcessedAt) 
                        ELSE NULL END) as avg_processing_hours
                FROM BookingCancellation
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Get cancellation stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Validate booking ownership
     * @param int $bookingId
     * @param int $userId
     * @return array
     */
    private function validateBookingOwnership($bookingId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ID, UserId, Status FROM Booking WHERE ID = ?
            ");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                return [
                    'valid' => false,
                    'error' => 'Booking not found'
                ];
            }
            
            // Allow admin or booking owner to cancel
            if ($userId && $booking['UserId'] != $userId) {
                return [
                    'valid' => false,
                    'error' => 'You can only cancel your own bookings'
                ];
            }
            
            return ['valid' => true, 'booking' => $booking];
            
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => 'Database error while validating booking'
            ];
        }
    }
    
    /**
     * Check if booking is eligible for cancellation
     * @param int $bookingId
     * @return array
     */
    private function checkCancellationEligibility($bookingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT b.Status, s.DepartureTime 
                FROM Booking b
                LEFT JOIN Bus bus ON b.BusID = bus.ID
                LEFT JOIN Schedule s ON bus.ID = s.BusID
                WHERE b.ID = ?
                ORDER BY s.DepartureTime ASC
                LIMIT 1
            ");
            $stmt->execute([$bookingId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'eligible' => false,
                    'error' => 'Booking details not found'
                ];
            }
            
            // Check booking status
            if ($result['Status'] === 'cancelled') {
                return [
                    'eligible' => false,
                    'error' => 'This booking has already been cancelled'
                ];
            }
            
            if ($result['Status'] === 'completed') {
                return [
                    'eligible' => false,
                    'error' => 'Cannot cancel a completed booking'
                ];
            }
            
            // Check if departure time allows cancellation (e.g., at least 2 hours before)
            if ($result['DepartureTime']) {
                $departureTime = strtotime($result['DepartureTime']);
                $currentTime = time();
                $hoursUntilDeparture = ($departureTime - $currentTime) / 3600;
                
                if ($hoursUntilDeparture < 2) {
                    return [
                        'eligible' => false,
                        'error' => 'Bookings can only be cancelled at least 2 hours before departure'
                    ];
                }
            }
            
            return ['eligible' => true];
            
        } catch (Exception $e) {
            return [
                'eligible' => false,
                'error' => 'Database error while checking eligibility'
            ];
        }
    }
    
    /**
     * Get pending cancellation by booking ID
     * @param int $bookingId
     * @return array|null
     */
    private function getPendingCancellationByBooking($bookingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM BookingCancellation 
                WHERE BookingID = ? AND Status = 'pending'
            ");
            $stmt->execute([$bookingId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Update booking status
     * @param int $bookingId
     * @param string $status
     * @return bool
     */
    private function updateBookingStatus($bookingId, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE Booking SET Status = ? WHERE ID = ?");
            return $stmt->execute([$status, $bookingId]);
        } catch (Exception $e) {
            error_log("Update booking status error: " . $e->getMessage());
            return false;
        }
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
        } catch (Exception $e) {
            error_log("Update seat status error: " . $e->getMessage());
            return true; // Continue if seat table doesn't exist
        }
    }
    
    /**
     * Update payment status for refunds
     * @param int $bookingId
     * @param string $status
     * @return bool
     */
    private function updatePaymentStatus($bookingId, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE Payment SET Status = ? WHERE BookingId = ?
            ");
            return $stmt->execute([$status, $bookingId]);
        } catch (Exception $e) {
            error_log("Update payment status error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get cancellations requiring admin attention
     * @return array
     */
    public function getCancellationsRequiringAttention() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT bc.*, b.SeatNumber, b.Fare,
                       u.Name as UserName, u.Email,
                       bus.BusNumber, r.Origin, r.Destination,
                       TIMESTAMPDIFF(HOUR, bc.RequestedAt, NOW()) as hours_pending
                FROM BookingCancellation bc
                JOIN Booking b ON bc.BookingID = b.ID
                LEFT JOIN User u ON bc.UserID = u.ID
                LEFT JOIN Bus bus ON b.BusID = bus.ID
                LEFT JOIN Route r ON bus.RouteId = r.ID
                WHERE bc.Status = 'pending'
                ORDER BY bc.RequestedAt ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get cancellations requiring attention error: " . $e->getMessage());
            return [];
        }
    }
}
?>