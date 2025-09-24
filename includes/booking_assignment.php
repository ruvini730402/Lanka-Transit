<?php
/**
 * Booking Assignment Helper
 * Handles assignment of existing bookings to newly registered users
 */

require_once __DIR__ . '/../classes/Database.php';

/**
 * Assign existing bookings to user based on phone number match
 * @param int $userId The newly created user ID
 * @param string $phoneNumber The phone number to match
 * @return array Result with success status and assignment details
 */
function assignExistingBookings($userId, $phoneNumber) {
    try {
        $database = new Database();
        $pdo = $database->getConnection();
        
        // Find bookings with matching phone number and NULL UserId (guest bookings)
        $findStmt = $pdo->prepare("SELECT ID FROM Booking WHERE PhoneNumber = ? AND UserId IS NULL");
        $findStmt->execute([$phoneNumber]);
        $bookingIds = $findStmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($bookingIds)) {
            return [
                'success' => true,
                'message' => 'No existing bookings found for this phone number.',
                'assignedCount' => 0
            ];
        }
        
        // Update bookings to assign them to the new user
        $updateStmt = $pdo->prepare("UPDATE Booking SET UserId = ? WHERE PhoneNumber = ? AND UserId IS NULL");
        $updateResult = $updateStmt->execute([$userId, $phoneNumber]);
        
        $assignedCount = $updateStmt->rowCount();
        
        if ($updateResult) {
            return [
                'success' => true,
                'message' => "Successfully assigned {$assignedCount} existing booking(s) to your account.",
                'assignedCount' => $assignedCount
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to assign existing bookings.',
                'assignedCount' => 0
            ];
        }
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error while assigning bookings: ' . $e->getMessage(),
            'assignedCount' => 0
        ];
    }
}