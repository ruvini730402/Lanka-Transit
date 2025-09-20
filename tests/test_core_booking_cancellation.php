<?php
/**
 * Test Booking Cancellation Functionality
 * Core booking cancellation functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/BookingCancellation.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Database.php';

class TestBookingCancellation {
    private $results = [];
    private $bookingCancellation;
    private $booking;
    
    public function __construct() {
        $this->bookingCancellation = new BookingCancellation();
        $this->booking = new Booking();
    }
    
    /**
     * Run all booking cancellation tests
     */
    public function runAllTests() {
        echo "<h2>❌ Booking Cancellation Tests</h2>";
        
        $this->testCancellationRequestCreation();
        $this->testCancellationValidation();
        $this->testEligibilityChecks();
        $this->testCancellationProcessing();
        $this->testStatusUpdates();
        $this->testCancellationRetrieval();
        
        $this->displayResults();
    }
    
    /**
     * Test cancellation request creation
     */
    private function testCancellationRequestCreation() {
        try {
            // First, create a test booking to cancel
            $testBookingData = [
                'bus_id' => 1,
                'passenger_name' => 'Test Passenger',
                'phone' => '0771234567',
                'email' => 'test@example.com',
                'nic' => '123456789V',
                'gender' => 'Male',
                'seat_number' => 'A1',
                'origin' => 'Badulla',
                'destination' => 'Matara',
                'fare' => 500.00,
                'journey_date' => date('Y-m-d', strtotime('+3 days')),
                'journey_time' => '08:00:00'
            ];
            
            $testBookingId = $this->booking->createBooking($testBookingData);
            
            if ($testBookingId) {
                $this->addResult('✅', 'Test Booking Creation', 'Created test booking for cancellation testing');
                
                // Create cancellation request
                $cancellationData = [
                    'booking_id' => $testBookingId,
                    'reason' => 'Emergency - Unable to travel',
                    'requested_by' => 'test@example.com'
                ];
                
                $cancellationId = $this->bookingCancellation->createCancellationRequest($cancellationData);
                
                if ($cancellationId && is_numeric($cancellationId)) {
                    $this->addResult('✅', 'Cancellation Request Creation', 'Cancellation request created successfully');
                    
                    // Verify the created cancellation request
                    $created = $this->bookingCancellation->getCancellationById($cancellationId);
                    
                    if ($created && $created['BookingID'] == $testBookingId) {
                        $this->addResult('✅', 'Cancellation Data Verification', 'Cancellation request data matches input');
                        
                        if ($created['Status'] === 'pending') {
                            $this->addResult('✅', 'Initial Status', 'Cancellation request has correct initial status');
                        } else {
                            $this->addResult('❌', 'Initial Status', 'Cancellation request has incorrect initial status');
                        }
                        
                        if ($created['Reason'] === $cancellationData['reason']) {
                            $this->addResult('✅', 'Reason Storage', 'Cancellation reason stored correctly');
                        } else {
                            $this->addResult('❌', 'Reason Storage', 'Cancellation reason not stored correctly');
                        }
                    } else {
                        $this->addResult('❌', 'Cancellation Data Verification', 'Cancellation request data does not match');
                    }
                    
                    // Store IDs for cleanup
                    $this->testBookingId = $testBookingId;
                    $this->testCancellationId = $cancellationId;
                } else {
                    $this->addResult('❌', 'Cancellation Request Creation', 'Failed to create cancellation request');
                }
            } else {
                $this->addResult('❌', 'Test Booking Creation', 'Failed to create test booking');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Cancellation Request Creation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test cancellation validation
     */
    private function testCancellationValidation() {
        try {
            // Test missing booking ID
            $invalidData1 = [
                'reason' => 'Test reason',
                'requested_by' => 'test@example.com'
            ];
            
            $result1 = $this->bookingCancellation->createCancellationRequest($invalidData1);
            
            if (!$result1) {
                $this->addResult('✅', 'Booking ID Validation', 'Correctly rejected request without booking ID');
            } else {
                $this->addResult('❌', 'Booking ID Validation', 'Incorrectly accepted request without booking ID');
                // Cleanup if created
                $this->bookingCancellation->deleteCancellation($result1);
            }
            
            // Test missing reason
            $invalidData2 = [
                'booking_id' => 999999,
                'requested_by' => 'test@example.com'
            ];
            
            $result2 = $this->bookingCancellation->createCancellationRequest($invalidData2);
            
            if (!$result2) {
                $this->addResult('✅', 'Reason Validation', 'Correctly rejected request without reason');
            } else {
                $this->addResult('❌', 'Reason Validation', 'Incorrectly accepted request without reason');
                // Cleanup if created
                $this->bookingCancellation->deleteCancellation($result2);
            }
            
            // Test invalid booking ID
            $invalidData3 = [
                'booking_id' => 999999,
                'reason' => 'Test reason',
                'requested_by' => 'test@example.com'
            ];
            
            $result3 = $this->bookingCancellation->createCancellationRequest($invalidData3);
            
            if (!$result3) {
                $this->addResult('✅', 'Invalid Booking Validation', 'Correctly rejected request for non-existent booking');
            } else {
                $this->addResult('❌', 'Invalid Booking Validation', 'Incorrectly accepted request for non-existent booking');
                // Cleanup if created
                $this->bookingCancellation->deleteCancellation($result3);
            }
            
            // Test duplicate cancellation request
            if (isset($this->testBookingId)) {
                $duplicateData = [
                    'booking_id' => $this->testBookingId,
                    'reason' => 'Duplicate test',
                    'requested_by' => 'test@example.com'
                ];
                
                $duplicateResult = $this->bookingCancellation->createCancellationRequest($duplicateData);
                
                if (!$duplicateResult) {
                    $this->addResult('✅', 'Duplicate Request Validation', 'Correctly rejected duplicate cancellation request');
                } else {
                    $this->addResult('❌', 'Duplicate Request Validation', 'Incorrectly accepted duplicate cancellation request');
                    // Cleanup if created
                    $this->bookingCancellation->deleteCancellation($duplicateResult);
                }
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Cancellation Validation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test eligibility checks
     */
    private function testEligibilityChecks() {
        try {
            if (isset($this->testBookingId)) {
                // Test eligibility for future booking
                $eligibility = $this->bookingCancellation->checkCancellationEligibility($this->testBookingId);
                
                if ($eligibility === true) {
                    $this->addResult('✅', 'Future Booking Eligibility', 'Future booking correctly eligible for cancellation');
                } else {
                    $this->addResult('❌', 'Future Booking Eligibility', 'Future booking incorrectly ineligible: ' . $eligibility);
                }
            }
            
            // Create a past booking to test ineligibility
            $pastBookingData = [
                'bus_id' => 1,
                'passenger_name' => 'Past Passenger',
                'phone' => '0771234567',
                'email' => 'past@example.com',
                'nic' => '987654321V',
                'gender' => 'Female',
                'seat_number' => 'B2',
                'origin' => 'Colombo',
                'destination' => 'Kandy',
                'fare' => 300.00,
                'journey_date' => date('Y-m-d', strtotime('-1 day')),
                'journey_time' => '10:00:00'
            ];
            
            $pastBookingId = $this->booking->createBooking($pastBookingData);
            
            if ($pastBookingId) {
                $this->addResult('✅', 'Past Booking Creation', 'Created past booking for eligibility testing');
                
                // Test eligibility for past booking
                $pastEligibility = $this->bookingCancellation->checkCancellationEligibility($pastBookingId);
                
                if ($pastEligibility !== true) {
                    $this->addResult('✅', 'Past Booking Ineligibility', 'Past booking correctly ineligible for cancellation');
                } else {
                    $this->addResult('❌', 'Past Booking Ineligibility', 'Past booking incorrectly eligible for cancellation');
                }
                
                // Cleanup past booking
                $database = new Database();
                $conn = $database->getConnection();
                $stmt = $conn->prepare("DELETE FROM Booking WHERE BookingID = ?");
                $stmt->execute([$pastBookingId]);
                
            } else {
                $this->addResult('❌', 'Past Booking Creation', 'Failed to create past booking for testing');
            }
            
            // Test eligibility for non-existent booking
            $nonExistentEligibility = $this->bookingCancellation->checkCancellationEligibility(999999);
            
            if ($nonExistentEligibility !== true) {
                $this->addResult('✅', 'Non-existent Booking Eligibility', 'Non-existent booking correctly ineligible');
            } else {
                $this->addResult('❌', 'Non-existent Booking Eligibility', 'Non-existent booking incorrectly eligible');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Eligibility Checks', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test cancellation processing
     */
    private function testCancellationProcessing() {
        try {
            if (isset($this->testCancellationId)) {
                // Test approval processing
                $approvalResult = $this->bookingCancellation->processCancellation(
                    $this->testCancellationId,
                    'approved',
                    'Test Admin',
                    'Cancellation approved for testing'
                );
                
                if ($approvalResult) {
                    $this->addResult('✅', 'Cancellation Approval', 'Cancellation request approved successfully');
                    
                    // Verify approval
                    $processed = $this->bookingCancellation->getCancellationById($this->testCancellationId);
                    
                    if ($processed && $processed['Status'] === 'approved') {
                        $this->addResult('✅', 'Approval Status Update', 'Cancellation status updated to approved');
                    } else {
                        $this->addResult('❌', 'Approval Status Update', 'Cancellation status not updated correctly');
                    }
                    
                    if ($processed && $processed['ProcessedBy'] === 'Test Admin') {
                        $this->addResult('✅', 'Processor Recording', 'Processor information recorded correctly');
                    } else {
                        $this->addResult('❌', 'Processor Recording', 'Processor information not recorded correctly');
                    }
                    
                    if ($processed && !empty($processed['ProcessedAt'])) {
                        $this->addResult('✅', 'Processing Timestamp', 'Processing timestamp recorded');
                    } else {
                        $this->addResult('❌', 'Processing Timestamp', 'Processing timestamp not recorded');
                    }
                } else {
                    $this->addResult('❌', 'Cancellation Approval', 'Failed to approve cancellation request');
                }
                
                // Create another test request for rejection testing
                $testBookingData2 = [
                    'bus_id' => 1,
                    'passenger_name' => 'Test Passenger 2',
                    'phone' => '0779876543',
                    'email' => 'test2@example.com',
                    'nic' => '567890123V',
                    'gender' => 'Female',
                    'seat_number' => 'C3',
                    'origin' => 'Galle',
                    'destination' => 'Colombo',
                    'fare' => 400.00,
                    'journey_date' => date('Y-m-d', strtotime('+5 days')),
                    'journey_time' => '14:00:00'
                ];
                
                $testBookingId2 = $this->booking->createBooking($testBookingData2);
                
                if ($testBookingId2) {
                    $cancellationData2 = [
                        'booking_id' => $testBookingId2,
                        'reason' => 'Test rejection',
                        'requested_by' => 'test2@example.com'
                    ];
                    
                    $cancellationId2 = $this->bookingCancellation->createCancellationRequest($cancellationData2);
                    
                    if ($cancellationId2) {
                        // Test rejection processing
                        $rejectionResult = $this->bookingCancellation->processCancellation(
                            $cancellationId2,
                            'rejected',
                            'Test Admin',
                            'Cancellation rejected for testing'
                        );
                        
                        if ($rejectionResult) {
                            $this->addResult('✅', 'Cancellation Rejection', 'Cancellation request rejected successfully');
                            
                            // Verify rejection
                            $rejected = $this->bookingCancellation->getCancellationById($cancellationId2);
                            
                            if ($rejected && $rejected['Status'] === 'rejected') {
                                $this->addResult('✅', 'Rejection Status Update', 'Cancellation status updated to rejected');
                            } else {
                                $this->addResult('❌', 'Rejection Status Update', 'Cancellation status not updated correctly');
                            }
                        } else {
                            $this->addResult('❌', 'Cancellation Rejection', 'Failed to reject cancellation request');
                        }
                        
                        // Cleanup
                        $this->bookingCancellation->deleteCancellation($cancellationId2);
                    }
                    
                    // Cleanup booking
                    $database = new Database();
                    $conn = $database->getConnection();
                    $stmt = $conn->prepare("DELETE FROM Booking WHERE BookingID = ?");
                    $stmt->execute([$testBookingId2]);
                }
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Cancellation Processing', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test status updates
     */
    private function testStatusUpdates() {
        try {
            if (isset($this->testCancellationId)) {
                // Test status update method
                $statusUpdateResult = $this->bookingCancellation->updateCancellationStatus(
                    $this->testCancellationId,
                    'processing'
                );
                
                if ($statusUpdateResult) {
                    $this->addResult('✅', 'Status Update Method', 'Cancellation status updated successfully');
                    
                    // Verify status update
                    $updated = $this->bookingCancellation->getCancellationById($this->testCancellationId);
                    
                    if ($updated && $updated['Status'] === 'processing') {
                        $this->addResult('✅', 'Status Update Verification', 'Status update reflected in database');
                    } else {
                        $this->addResult('❌', 'Status Update Verification', 'Status update not reflected correctly');
                    }
                } else {
                    $this->addResult('❌', 'Status Update Method', 'Failed to update cancellation status');
                }
                
                // Test invalid status
                $invalidStatusResult = $this->bookingCancellation->updateCancellationStatus(
                    $this->testCancellationId,
                    'invalid_status'
                );
                
                if (!$invalidStatusResult) {
                    $this->addResult('✅', 'Invalid Status Rejection', 'Invalid status correctly rejected');
                } else {
                    $this->addResult('❌', 'Invalid Status Rejection', 'Invalid status incorrectly accepted');
                }
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Status Updates', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test cancellation retrieval methods
     */
    private function testCancellationRetrieval() {
        try {
            // Test get cancellations by status
            $pendingCancellations = $this->bookingCancellation->getCancellationsByStatus('pending');
            
            if (is_array($pendingCancellations)) {
                $this->addResult('✅', 'Get by Status Method', 'Retrieved cancellations by status successfully');
            } else {
                $this->addResult('❌', 'Get by Status Method', 'Failed to retrieve cancellations by status');
            }
            
            // Test get cancellations by booking ID
            if (isset($this->testBookingId)) {
                $bookingCancellations = $this->bookingCancellation->getCancellationsByBookingId($this->testBookingId);
                
                if (is_array($bookingCancellations)) {
                    $this->addResult('✅', 'Get by Booking ID', 'Retrieved cancellations by booking ID successfully');
                    
                    if (count($bookingCancellations) > 0) {
                        $this->addResult('✅', 'Booking ID Filter', 'Found cancellations for the test booking');
                    } else {
                        $this->addResult('⚠️', 'Booking ID Filter', 'No cancellations found for test booking');
                    }
                } else {
                    $this->addResult('❌', 'Get by Booking ID', 'Failed to retrieve cancellations by booking ID');
                }
            }
            
            // Test get all cancellations
            $allCancellations = $this->bookingCancellation->getAllCancellations();
            
            if (is_array($allCancellations)) {
                $this->addResult('✅', 'Get All Method', 'Retrieved all cancellations successfully');
                
                if (count($allCancellations) > 0) {
                    $this->addResult('✅', 'Cancellations Exist', 'Found ' . count($allCancellations) . ' total cancellations');
                } else {
                    $this->addResult('⚠️', 'Cancellations Exist', 'No cancellations found in database');
                }
            } else {
                $this->addResult('❌', 'Get All Method', 'Failed to retrieve all cancellations');
            }
            
            // Cleanup test data
            if (isset($this->testCancellationId)) {
                $this->bookingCancellation->deleteCancellation($this->testCancellationId);
            }
            
            if (isset($this->testBookingId)) {
                $database = new Database();
                $conn = $database->getConnection();
                $stmt = $conn->prepare("DELETE FROM Booking WHERE BookingID = ?");
                $stmt->execute([$this->testBookingId]);
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Cancellation Retrieval', 'Exception: ' . $e->getMessage());
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
        echo "<p><strong>Booking Cancellation Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_booking_cancellation.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Booking Cancellation Tests</title>
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
            <h1>Lanka Transit - Booking Cancellation Tests</h1>
            <?php
            $test = new TestBookingCancellation();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>