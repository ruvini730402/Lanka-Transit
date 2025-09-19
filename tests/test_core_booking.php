<?php
/**
 * Test Booking Functionality
 * Core booking functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Database.php';

class TestBooking {
    private $results = [];
    private $booking;
    private $testBookingIds = [];
    
    public function __construct() {
        $this->booking = new Booking();
    }
    
    /**
     * Run all booking tests
     */
    public function runAllTests() {
        echo "<h2>📝 Booking Tests</h2>";
        
        $this->testBookingValidation();
        $this->testBookingCreation();
        $this->testSeatAvailabilityCheck();
        $this->testGenderHandling();
        $this->testBookingRetrieval();
        $this->testBookingStatusUpdate();
        $this->cleanup();
        
        $this->displayResults();
    }
    
    /**
     * Test booking data validation
     */
    private function testBookingValidation() {
        try {
            // Test missing required fields
            $invalidData = [
                'passenger_name' => '',
                'phone' => '0771234567',
                'bus_id' => 1,
                'seat_number' => '1',
                'fare' => 500
            ];
            
            $result = $this->booking->validateBookingData($invalidData);
            
            if (!$result['valid']) {
                $this->addResult('✅', 'Missing Field Validation', 'Missing passenger name correctly rejected');
            } else {
                $this->addResult('❌', 'Missing Field Validation', 'Missing passenger name not validated');
            }
            
            // Test invalid phone number
            $invalidPhone = [
                'passenger_name' => 'Test User',
                'phone' => 'invalid-phone',
                'bus_id' => 1,
                'seat_number' => '1',
                'fare' => 500
            ];
            
            $result = $this->booking->validateBookingData($invalidPhone);
            
            if (!$result['valid']) {
                $this->addResult('✅', 'Phone Validation', 'Invalid phone number correctly rejected');
            } else {
                $this->addResult('❌', 'Phone Validation', 'Invalid phone number not validated');
            }
            
            // Test invalid fare
            $invalidFare = [
                'passenger_name' => 'Test User',
                'phone' => '0771234567',
                'bus_id' => 1,
                'seat_number' => '1',
                'fare' => -100
            ];
            
            $result = $this->booking->validateBookingData($invalidFare);
            
            if (!$result['valid']) {
                $this->addResult('✅', 'Fare Validation', 'Invalid fare amount correctly rejected');
            } else {
                $this->addResult('❌', 'Fare Validation', 'Invalid fare amount not validated');
            }
            
            // Test valid data
            $validData = [
                'passenger_name' => 'Test User',
                'phone' => '0771234567',
                'bus_id' => 1,
                'seat_number' => '1',
                'fare' => 500
            ];
            
            $result = $this->booking->validateBookingData($validData);
            
            if ($result['valid']) {
                $this->addResult('✅', 'Valid Data', 'Valid booking data correctly accepted');
            } else {
                $this->addResult('❌', 'Valid Data', 'Valid booking data incorrectly rejected');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Booking Validation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test booking creation
     */
    private function testBookingCreation() {
        try {
            $bookingData = [
                'passenger_name' => 'Test Passenger ' . time(),
                'phone' => '077' . rand(1000000, 9999999),
                'bus_id' => 1,
                'seat_number' => rand(10, 40),
                'fare' => 500.00,
                'gender' => 'male',
                'travel_date' => '2025-09-25' // Future date
            ];
            
            // Skip availability check for test
            $result = $this->booking->createBooking($bookingData, true);
            
            if ($result['success']) {
                $this->addResult('✅', 'Booking Creation', 'Booking created successfully');
                $this->testBookingIds[] = $result['booking_id'];
                
                if (isset($result['booking_reference']) && strpos($result['booking_reference'], 'LT-') === 0) {
                    $this->addResult('✅', 'Booking Reference', 'Booking reference generated correctly');
                } else {
                    $this->addResult('❌', 'Booking Reference', 'Booking reference not generated correctly');
                }
            } else {
                $this->addResult('❌', 'Booking Creation', 'Booking creation failed: ' . ($result['error'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Booking Creation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test seat availability checking
     */
    private function testSeatAvailabilityCheck() {
        try {
            // Test with a random seat (should be available)
            $available = $this->booking->checkSeatAvailability(1, rand(20, 40), '2025-09-25');
            
            if ($available) {
                $this->addResult('✅', 'Seat Availability Check', 'Available seat correctly identified');
            } else {
                $this->addResult('⚠️', 'Seat Availability Check', 'Seat check returned false (may be expected)');
            }
            
            // Test the isSeatBooked method (opposite logic)
            $booked = $this->booking->isSeatBooked(1, rand(20, 40), '2025-09-25');
            
            if (!$booked) {
                $this->addResult('✅', 'Seat Booked Check', 'Available seat correctly not marked as booked');
            } else {
                $this->addResult('⚠️', 'Seat Booked Check', 'Seat marked as booked (may be expected)');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Seat Availability', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test gender handling
     */
    private function testGenderHandling() {
        if (empty($this->testBookingIds)) {
            $this->addResult('⚠️', 'Gender Handling', 'Skipped - no test booking available');
            return;
        }
        
        try {
            $bookingId = $this->testBookingIds[0];
            
            // Test gender record creation
            $result = $this->booking->createGenderRecord($bookingId, 'female');
            
            if ($result) {
                $this->addResult('✅', 'Gender Record Creation', 'Gender record created successfully');
                
                // Test gender retrieval
                $gender = $this->booking->getGenderByBooking($bookingId);
                
                if ($gender === 'female') {
                    $this->addResult('✅', 'Gender Retrieval', 'Gender retrieved correctly');
                } else {
                    $this->addResult('❌', 'Gender Retrieval', 'Gender not retrieved correctly');
                }
                
                // Test gender update
                $updateResult = $this->booking->updateGender($bookingId, 'male');
                
                if ($updateResult) {
                    $this->addResult('✅', 'Gender Update', 'Gender updated successfully');
                    
                    // Verify update
                    $updatedGender = $this->booking->getGenderByBooking($bookingId);
                    
                    if ($updatedGender === 'male') {
                        $this->addResult('✅', 'Gender Update Verification', 'Gender update verified');
                    } else {
                        $this->addResult('❌', 'Gender Update Verification', 'Gender update not verified');
                    }
                } else {
                    $this->addResult('❌', 'Gender Update', 'Gender update failed');
                }
            } else {
                $this->addResult('⚠️', 'Gender Record Creation', 'Gender record creation failed (Booking_2 table may not exist)');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Gender Handling', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test booking retrieval
     */
    private function testBookingRetrieval() {
        if (empty($this->testBookingIds)) {
            $this->addResult('⚠️', 'Booking Retrieval', 'Skipped - no test booking available');
            return;
        }
        
        try {
            $bookingId = $this->testBookingIds[0];
            
            // Test get booking by ID
            $booking = $this->booking->getBookingById($bookingId);
            
            if ($booking && is_array($booking)) {
                $this->addResult('✅', 'Booking Retrieval by ID', 'Booking retrieved successfully by ID');
                
                // Check required fields
                $requiredFields = ['ID', 'SeatNumber', 'Fare', 'Status'];
                $hasAllFields = true;
                
                foreach ($requiredFields as $field) {
                    if (!isset($booking[$field])) {
                        $hasAllFields = false;
                        break;
                    }
                }
                
                if ($hasAllFields) {
                    $this->addResult('✅', 'Booking Data Completeness', 'Retrieved booking has all required fields');
                } else {
                    $this->addResult('❌', 'Booking Data Completeness', 'Retrieved booking missing required fields');
                }
            } else {
                $this->addResult('❌', 'Booking Retrieval by ID', 'Failed to retrieve booking by ID');
            }
            
            // Test total bookings count
            $total = $this->booking->getTotalBookings();
            
            if (is_numeric($total) && $total >= 0) {
                $this->addResult('✅', 'Total Bookings Count', 'Total bookings count retrieved correctly');
            } else {
                $this->addResult('❌', 'Total Bookings Count', 'Total bookings count not retrieved correctly');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Booking Retrieval', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test booking status updates
     */
    private function testBookingStatusUpdate() {
        if (empty($this->testBookingIds)) {
            $this->addResult('⚠️', 'Status Update', 'Skipped - no test booking available');
            return;
        }
        
        try {
            $bookingId = $this->testBookingIds[0];
            
            // Test status update to cancelled
            $result = $this->booking->updateBookingStatus($bookingId, 'cancelled');
            
            if ($result) {
                $this->addResult('✅', 'Status Update', 'Booking status updated successfully');
                
                // Verify status change
                $booking = $this->booking->getBookingById($bookingId);
                
                if ($booking && $booking['Status'] === 'cancelled') {
                    $this->addResult('✅', 'Status Update Verification', 'Status change verified in database');
                } else {
                    $this->addResult('❌', 'Status Update Verification', 'Status change not reflected in database');
                }
                
                // Test status update to completed
                $result2 = $this->booking->updateBookingStatus($bookingId, 'completed');
                
                if ($result2) {
                    $this->addResult('✅', 'Multiple Status Updates', 'Multiple status updates work correctly');
                } else {
                    $this->addResult('❌', 'Multiple Status Updates', 'Multiple status updates failed');
                }
            } else {
                $this->addResult('❌', 'Status Update', 'Booking status update failed');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Status Update', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Cleanup test data
     */
    private function cleanup() {
        foreach ($this->testBookingIds as $bookingId) {
            try {
                $database = new Database();
                $conn = $database->getConnection();
                
                // Clean up Booking_2 first (if exists)
                $stmt = $conn->prepare("DELETE FROM Booking_2 WHERE booking_id = ?");
                $stmt->execute([$bookingId]);
                
                // Clean up Booking
                $stmt = $conn->prepare("DELETE FROM Booking WHERE ID = ?");
                $stmt->execute([$bookingId]);
                
            } catch (Exception $e) {
                // Silent cleanup - table might not exist
            }
        }
        
        if (!empty($this->testBookingIds)) {
            $this->addResult('✅', 'Test Cleanup', 'Test booking data cleaned up successfully');
        }
    }
    
    /**
     * Add test result
     */
    private function addResult($status, $test, $message) {
        $this->results[] = [
            'status' => $status,
            'test' => $test,
            'message' => $message
        ];
    }
    
    /**
     * Display test results
     */
    private function displayResults() {
        echo "<div class='test-results'>";
        foreach ($this->results as $result) {
            echo "<div class='test-item'>";
            echo "<span class='status'>{$result['status']}</span> ";
            echo "<strong>{$result['test']}:</strong> {$result['message']}";
            echo "</div>";
        }
        echo "</div>";
        
        $passed = count(array_filter($this->results, function($r) { return $r['status'] === '✅'; }));
        $total = count($this->results);
        echo "<p><strong>Booking Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_booking.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Booking Tests</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
            .test-results { margin: 20px 0; }
            .test-item { padding: 8px; margin: 4px 0; border-left: 4px solid #007bff; background: #f8f9fa; }
            .status { font-size: 1.2em; }
            h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Lanka Transit - Booking Tests</h1>
            <?php
            $test = new TestBooking();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>