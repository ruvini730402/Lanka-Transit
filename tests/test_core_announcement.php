<?php
/**
 * Test Announcement Functionality
 * Core announcement functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/Announcement.php';
require_once __DIR__ . '/../classes/Database.php';

class TestAnnouncement {
    private $results = [];
    private $announcement;
    
    public function __construct() {
        $this->announcement = new Announcement();
    }
    
    /**
     * Run all announcement tests
     */
    public function runAllTests() {
        echo "<h2>📢 Announcement Tests</h2>";
        
        $this->testAnnouncementCreation();
        $this->testAnnouncementRetrieval();
        $this->testAnnouncementValidation();
        $this->testActiveAnnouncements();
        $this->testAnnouncementPriority();
        $this->testAnnouncementStatusUpdates();
        
        $this->displayResults();
    }
    
    /**
     * Test announcement creation
     */
    private function testAnnouncementCreation() {
        try {
            $testData = [
                'title' => 'Test Announcement',
                'content' => 'This is a test announcement content.',
                'priority' => 'medium',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+7 days')),
                'is_active' => true
            ];
            
            $result = $this->announcement->createAnnouncement($testData);
            
            if ($result && is_numeric($result)) {
                $this->addResult('✅', 'Announcement Creation', 'Announcement created successfully with ID: ' . $result);
                
                // Store the ID for cleanup
                $this->testAnnouncementId = $result;
                
                // Verify the created announcement
                $created = $this->announcement->getAnnouncementById($result);
                
                if ($created && $created['Title'] === $testData['title']) {
                    $this->addResult('✅', 'Creation Verification', 'Created announcement data matches input');
                } else {
                    $this->addResult('❌', 'Creation Verification', 'Created announcement data does not match');
                }
            } else {
                $this->addResult('❌', 'Announcement Creation', 'Failed to create announcement');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Announcement Creation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test announcement retrieval methods
     */
    private function testAnnouncementRetrieval() {
        try {
            // Test get all announcements
            $allAnnouncements = $this->announcement->getAllAnnouncements();
            
            if (is_array($allAnnouncements)) {
                $this->addResult('✅', 'Get All Announcements', 'Retrieved all announcements successfully');
                
                if (count($allAnnouncements) > 0) {
                    $this->addResult('✅', 'Announcements Exist', 'Found ' . count($allAnnouncements) . ' announcements');
                    
                    // Test single announcement retrieval
                    $firstAnnouncement = $allAnnouncements[0];
                    $singleAnnouncement = $this->announcement->getAnnouncementById($firstAnnouncement['AnnouncementID']);
                    
                    if ($singleAnnouncement && $singleAnnouncement['AnnouncementID'] === $firstAnnouncement['AnnouncementID']) {
                        $this->addResult('✅', 'Single Announcement Retrieval', 'Retrieved single announcement correctly');
                    } else {
                        $this->addResult('❌', 'Single Announcement Retrieval', 'Failed to retrieve single announcement');
                    }
                } else {
                    $this->addResult('⚠️', 'Announcements Exist', 'No announcements found in database');
                }
            } else {
                $this->addResult('❌', 'Get All Announcements', 'Failed to retrieve announcements');
            }
            
            // Test retrieval with invalid ID
            $invalidAnnouncement = $this->announcement->getAnnouncementById(99999);
            
            if (!$invalidAnnouncement) {
                $this->addResult('✅', 'Invalid ID Handling', 'Correctly handled invalid announcement ID');
            } else {
                $this->addResult('❌', 'Invalid ID Handling', 'Did not handle invalid announcement ID correctly');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Announcement Retrieval', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test announcement validation
     */
    private function testAnnouncementValidation() {
        try {
            // Test missing title
            $invalidData1 = [
                'content' => 'Test content',
                'priority' => 'high',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+7 days'))
            ];
            
            $result1 = $this->announcement->createAnnouncement($invalidData1);
            
            if (!$result1) {
                $this->addResult('✅', 'Title Validation', 'Correctly rejected announcement without title');
            } else {
                $this->addResult('❌', 'Title Validation', 'Incorrectly accepted announcement without title');
                // Cleanup if created
                $this->announcement->deleteAnnouncement($result1);
            }
            
            // Test missing content
            $invalidData2 = [
                'title' => 'Test Title',
                'priority' => 'high',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+7 days'))
            ];
            
            $result2 = $this->announcement->createAnnouncement($invalidData2);
            
            if (!$result2) {
                $this->addResult('✅', 'Content Validation', 'Correctly rejected announcement without content');
            } else {
                $this->addResult('❌', 'Content Validation', 'Incorrectly accepted announcement without content');
                // Cleanup if created
                $this->announcement->deleteAnnouncement($result2);
            }
            
            // Test invalid priority
            $invalidData3 = [
                'title' => 'Test Title',
                'content' => 'Test content',
                'priority' => 'invalid_priority',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+7 days'))
            ];
            
            $result3 = $this->announcement->createAnnouncement($invalidData3);
            
            if (!$result3) {
                $this->addResult('✅', 'Priority Validation', 'Correctly rejected announcement with invalid priority');
            } else {
                $this->addResult('❌', 'Priority Validation', 'Incorrectly accepted announcement with invalid priority');
                // Cleanup if created
                $this->announcement->deleteAnnouncement($result3);
            }
            
            // Test invalid date range (end before start)
            $invalidData4 = [
                'title' => 'Test Title',
                'content' => 'Test content',
                'priority' => 'medium',
                'start_date' => date('Y-m-d', strtotime('+7 days')),
                'end_date' => date('Y-m-d')
            ];
            
            $result4 = $this->announcement->createAnnouncement($invalidData4);
            
            if (!$result4) {
                $this->addResult('✅', 'Date Range Validation', 'Correctly rejected announcement with invalid date range');
            } else {
                $this->addResult('❌', 'Date Range Validation', 'Incorrectly accepted announcement with invalid date range');
                // Cleanup if created
                $this->announcement->deleteAnnouncement($result4);
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Announcement Validation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test active announcements functionality
     */
    private function testActiveAnnouncements() {
        try {
            // Create test announcements with different date ranges
            $activeData = [
                'title' => 'Active Test Announcement',
                'content' => 'This announcement is currently active.',
                'priority' => 'high',
                'start_date' => date('Y-m-d', strtotime('-1 day')),
                'end_date' => date('Y-m-d', strtotime('+1 day')),
                'is_active' => true
            ];
            
            $expiredData = [
                'title' => 'Expired Test Announcement',
                'content' => 'This announcement has expired.',
                'priority' => 'medium',
                'start_date' => date('Y-m-d', strtotime('-7 days')),
                'end_date' => date('Y-m-d', strtotime('-1 day')),
                'is_active' => true
            ];
            
            $futureData = [
                'title' => 'Future Test Announcement',
                'content' => 'This announcement is for the future.',
                'priority' => 'low',
                'start_date' => date('Y-m-d', strtotime('+1 day')),
                'end_date' => date('Y-m-d', strtotime('+7 days')),
                'is_active' => true
            ];
            
            $activeId = $this->announcement->createAnnouncement($activeData);
            $expiredId = $this->announcement->createAnnouncement($expiredData);
            $futureId = $this->announcement->createAnnouncement($futureData);
            
            if ($activeId && $expiredId && $futureId) {
                $this->addResult('✅', 'Test Data Creation', 'Created test announcements for active testing');
                
                // Test active announcements retrieval
                $activeAnnouncements = $this->announcement->getActiveAnnouncements();
                
                if (is_array($activeAnnouncements)) {
                    $this->addResult('✅', 'Active Announcements Retrieval', 'Retrieved active announcements');
                    
                    // Check if active announcement is in the results
                    $foundActive = false;
                    $foundExpired = false;
                    $foundFuture = false;
                    
                    foreach ($activeAnnouncements as $announcement) {
                        if ($announcement['AnnouncementID'] == $activeId) {
                            $foundActive = true;
                        }
                        if ($announcement['AnnouncementID'] == $expiredId) {
                            $foundExpired = true;
                        }
                        if ($announcement['AnnouncementID'] == $futureId) {
                            $foundFuture = true;
                        }
                    }
                    
                    if ($foundActive) {
                        $this->addResult('✅', 'Active Announcement Inclusion', 'Current active announcement included');
                    } else {
                        $this->addResult('❌', 'Active Announcement Inclusion', 'Current active announcement not included');
                    }
                    
                    if (!$foundExpired) {
                        $this->addResult('✅', 'Expired Announcement Exclusion', 'Expired announcement correctly excluded');
                    } else {
                        $this->addResult('❌', 'Expired Announcement Exclusion', 'Expired announcement incorrectly included');
                    }
                    
                    if (!$foundFuture) {
                        $this->addResult('✅', 'Future Announcement Exclusion', 'Future announcement correctly excluded');
                    } else {
                        $this->addResult('❌', 'Future Announcement Exclusion', 'Future announcement incorrectly included');
                    }
                } else {
                    $this->addResult('❌', 'Active Announcements Retrieval', 'Failed to retrieve active announcements');
                }
                
                // Cleanup test announcements
                $this->announcement->deleteAnnouncement($activeId);
                $this->announcement->deleteAnnouncement($expiredId);
                $this->announcement->deleteAnnouncement($futureId);
                
            } else {
                $this->addResult('❌', 'Test Data Creation', 'Failed to create test announcements');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Active Announcements', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test announcement priority functionality
     */
    private function testAnnouncementPriority() {
        try {
            // Create announcements with different priorities
            $priorities = ['high', 'medium', 'low'];
            $createdIds = [];
            
            foreach ($priorities as $priority) {
                $data = [
                    'title' => ucfirst($priority) . ' Priority Test',
                    'content' => 'This is a ' . $priority . ' priority announcement.',
                    'priority' => $priority,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d', strtotime('+7 days')),
                    'is_active' => true
                ];
                
                $id = $this->announcement->createAnnouncement($data);
                if ($id) {
                    $createdIds[] = $id;
                }
            }
            
            if (count($createdIds) === 3) {
                $this->addResult('✅', 'Priority Test Data', 'Created announcements with different priorities');
                
                // Test if announcements are ordered by priority
                $announcements = $this->announcement->getActiveAnnouncements();
                
                if (is_array($announcements) && count($announcements) >= 3) {
                    $priorityOrder = ['high', 'medium', 'low'];
                    $isCorrectOrder = true;
                    $previousPriorityIndex = -1;
                    
                    foreach ($announcements as $announcement) {
                        if (in_array($announcement['AnnouncementID'], $createdIds)) {
                            $currentPriorityIndex = array_search($announcement['Priority'], $priorityOrder);
                            if ($currentPriorityIndex < $previousPriorityIndex) {
                                $isCorrectOrder = false;
                                break;
                            }
                            $previousPriorityIndex = $currentPriorityIndex;
                        }
                    }
                    
                    if ($isCorrectOrder) {
                        $this->addResult('✅', 'Priority Ordering', 'Announcements ordered correctly by priority');
                    } else {
                        $this->addResult('❌', 'Priority Ordering', 'Announcements not ordered correctly by priority');
                    }
                } else {
                    $this->addResult('⚠️', 'Priority Ordering', 'Insufficient announcements to test priority ordering');
                }
                
                // Cleanup
                foreach ($createdIds as $id) {
                    $this->announcement->deleteAnnouncement($id);
                }
                
            } else {
                $this->addResult('❌', 'Priority Test Data', 'Failed to create priority test announcements');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Announcement Priority', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test announcement status updates
     */
    private function testAnnouncementStatusUpdates() {
        try {
            // Create test announcement
            $testData = [
                'title' => 'Status Update Test',
                'content' => 'Testing status updates.',
                'priority' => 'medium',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+7 days')),
                'is_active' => true
            ];
            
            $testId = $this->announcement->createAnnouncement($testData);
            
            if ($testId) {
                $this->addResult('✅', 'Status Test Setup', 'Created announcement for status testing');
                
                // Test deactivation
                $deactivateResult = $this->announcement->updateAnnouncementStatus($testId, false);
                
                if ($deactivateResult) {
                    $this->addResult('✅', 'Announcement Deactivation', 'Successfully deactivated announcement');
                    
                    // Verify deactivation
                    $deactivated = $this->announcement->getAnnouncementById($testId);
                    if ($deactivated && !$deactivated['IsActive']) {
                        $this->addResult('✅', 'Deactivation Verification', 'Announcement status correctly updated to inactive');
                    } else {
                        $this->addResult('❌', 'Deactivation Verification', 'Announcement status not updated correctly');
                    }
                } else {
                    $this->addResult('❌', 'Announcement Deactivation', 'Failed to deactivate announcement');
                }
                
                // Test reactivation
                $reactivateResult = $this->announcement->updateAnnouncementStatus($testId, true);
                
                if ($reactivateResult) {
                    $this->addResult('✅', 'Announcement Reactivation', 'Successfully reactivated announcement');
                    
                    // Verify reactivation
                    $reactivated = $this->announcement->getAnnouncementById($testId);
                    if ($reactivated && $reactivated['IsActive']) {
                        $this->addResult('✅', 'Reactivation Verification', 'Announcement status correctly updated to active');
                    } else {
                        $this->addResult('❌', 'Reactivation Verification', 'Announcement status not updated correctly');
                    }
                } else {
                    $this->addResult('❌', 'Announcement Reactivation', 'Failed to reactivate announcement');
                }
                
                // Test deletion
                $deleteResult = $this->announcement->deleteAnnouncement($testId);
                
                if ($deleteResult) {
                    $this->addResult('✅', 'Announcement Deletion', 'Successfully deleted announcement');
                    
                    // Verify deletion
                    $deleted = $this->announcement->getAnnouncementById($testId);
                    if (!$deleted) {
                        $this->addResult('✅', 'Deletion Verification', 'Announcement correctly removed from database');
                    } else {
                        $this->addResult('❌', 'Deletion Verification', 'Announcement not removed from database');
                    }
                } else {
                    $this->addResult('❌', 'Announcement Deletion', 'Failed to delete announcement');
                }
                
            } else {
                $this->addResult('❌', 'Status Test Setup', 'Failed to create announcement for status testing');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Status Updates', 'Exception: ' . $e->getMessage());
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
        echo "<p><strong>Announcement Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_announcement.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Announcement Tests</title>
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
            <h1>Lanka Transit - Announcement Tests</h1>
            <?php
            $test = new TestAnnouncement();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>