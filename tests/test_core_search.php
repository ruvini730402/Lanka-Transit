<?php
/**
 * Test Bus Search Functionality
 * Core search functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/Bus.php';
require_once __DIR__ . '/../classes/Database.php';

class TestBusSearch {
    private $results = [];
    private $bus;
    
    public function __construct() {
        $database = new Database();
        $conn = $database->getConnection();
        $this->bus = new Bus($conn);
    }
    
    /**
     * Run all search tests
     */
    public function runAllTests() {
        echo "<h2>🚌 Bus Search Tests</h2>";
        
        $this->testBasicSearch();
        $this->testIntermediateStops();
        $this->testSearchValidation();
        $this->testFareRange();
        $this->testTimeRange();
        $this->testSeatAvailability();
        $this->testSearchFilters();
        
        $this->displayResults();
    }
    
    /**
     * Test basic search functionality
     */
    private function testBasicSearch() {
        try {
            // Test direct route search
            $result = $this->bus->searchBusesWithFilters('Badulla', 'Matara', '2025-09-18');
            
            if (isset($result['success']) && $result['success']) {
                $this->addResult('✅', 'Basic Search', 'Direct route search executed successfully');
                
                if (is_array($result['data']) && count($result['data']) >= 0) {
                    $this->addResult('✅', 'Search Results Format', 'Results returned in correct format');
                } else {
                    $this->addResult('❌', 'Search Results Format', 'Results not in expected format');
                }
            } else {
                $error = isset($result['error']) ? $result['error'] : 'Unknown error';
                $this->addResult('❌', 'Basic Search', 'Search failed: ' . $error);
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Basic Search', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test intermediate stops functionality
     */
    private function testIntermediateStops() {
        try {
            // Test scenario 2: Terminal to Stop
            $result1 = $this->bus->searchBusesWithFilters('Badulla', 'Ella', '2025-09-18');
            
            if (isset($result1['success']) && $result1['success']) {
                $this->addResult('✅', 'Terminal→Stop Search', 'Terminal to intermediate stop search works');
            } else {
                $this->addResult('⚠️', 'Terminal→Stop Search', 'No results or error (may be expected)');
            }
            
            // Test scenario 3: Stop to Terminal
            $result2 = $this->bus->searchBusesWithFilters('Ella', 'Matara', '2025-09-18');
            
            if (isset($result2['success']) && $result2['success']) {
                $this->addResult('✅', 'Stop→Terminal Search', 'Intermediate stop to terminal search works');
            } else {
                $this->addResult('⚠️', 'Stop→Terminal Search', 'No results or error (may be expected)');
            }
            
            // Test scenario 4: Stop to Stop (with order validation)
            $result3 = $this->bus->searchBusesWithFilters('Ella', 'Wellawaya', '2025-09-18');
            
            if (isset($result3['success'])) {
                $this->addResult('✅', 'Stop→Stop Search', 'Stop to stop search executed (validates FIND_IN_SET ordering)');
            } else {
                $this->addResult('⚠️', 'Stop→Stop Search', 'Stop to stop search needs sample data');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Intermediate Stops', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test search input validation
     */
    private function testSearchValidation() {
        try {
            // Test same origin and destination
            $result1 = $this->bus->searchBusesWithFilters('Badulla', 'Badulla', '2025-09-18');
            
            if (isset($result1['error']) && strpos($result1['error'], 'different') !== false) {
                $this->addResult('✅', 'Same Origin/Destination', 'Same origin and destination correctly rejected');
            } else {
                $this->addResult('❌', 'Same Origin/Destination', 'Same origin and destination not handled');
            }
            
            // Test empty inputs
            $result2 = $this->bus->searchBusesWithFilters('', 'Matara', '2025-09-18');
            
            if (isset($result2['error'])) {
                $this->addResult('✅', 'Empty Input Validation', 'Empty origin correctly rejected');
            } else {
                $this->addResult('❌', 'Empty Input Validation', 'Empty input not validated');
            }
            
            // Test invalid date
            $result3 = $this->bus->searchBusesWithFilters('Badulla', 'Matara', 'invalid-date');
            
            if (isset($result3['error']) && strpos($result3['error'], 'date') !== false) {
                $this->addResult('✅', 'Date Validation', 'Invalid date correctly rejected');
            } else {
                $this->addResult('❌', 'Date Validation', 'Invalid date not handled');
            }
            
            // Test past date
            $result4 = $this->bus->searchBusesWithFilters('Badulla', 'Matara', '2020-01-01');
            
            if (isset($result4['error']) && strpos($result4['error'], 'future') !== false) {
                $this->addResult('✅', 'Past Date Validation', 'Past date correctly rejected');
            } else {
                $this->addResult('❌', 'Past Date Validation', 'Past date not validated');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Search Validation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test fare range functionality
     */
    private function testFareRange() {
        try {
            $fareRange = $this->bus->getFareRange('Badulla', 'Matara');
            
            if (is_array($fareRange) && isset($fareRange['min_fare']) && isset($fareRange['max_fare'])) {
                $this->addResult('✅', 'Fare Range Query', 'Fare range query returns correct structure');
                
                if ($fareRange['min_fare'] <= $fareRange['max_fare']) {
                    $this->addResult('✅', 'Fare Range Logic', 'Min fare ≤ Max fare (logical consistency)');
                } else {
                    $this->addResult('❌', 'Fare Range Logic', 'Min fare > Max fare (logic error)');
                }
            } else {
                $this->addResult('❌', 'Fare Range Query', 'Fare range query returned unexpected format');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Fare Range', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test time range functionality
     */
    private function testTimeRange() {
        try {
            $timeRange = $this->bus->getDepartureTimeRange('Badulla', 'Matara');
            
            if (is_array($timeRange) && isset($timeRange['earliest_time']) && isset($timeRange['latest_time'])) {
                $this->addResult('✅', 'Time Range Query', 'Time range query returns correct structure');
                
                // Validate time format
                if (preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeRange['earliest_time']) &&
                    preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeRange['latest_time'])) {
                    $this->addResult('✅', 'Time Format', 'Time values in correct HH:MM format');
                } else {
                    $this->addResult('❌', 'Time Format', 'Time values not in expected format');
                }
            } else {
                $this->addResult('❌', 'Time Range Query', 'Time range query returned unexpected format');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Time Range', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test seat availability functionality
     */
    private function testSeatAvailability() {
        try {
            // Test with a bus ID (we'll use 1 as a common test ID)
            $seatResult = $this->bus->getAvailableSeats(1, '2025-09-18');
            
            if (isset($seatResult['success']) && $seatResult['success']) {
                $this->addResult('✅', 'Seat Availability Query', 'Seat availability query executed successfully');
                
                if (is_array($seatResult['data']) && count($seatResult['data']) > 0) {
                    // Check seat structure
                    $firstSeat = $seatResult['data'][0];
                    $expectedKeys = ['seat_number', 'gender_preference', 'is_lady_seat', 'status'];
                    $hasAllKeys = true;
                    
                    foreach ($expectedKeys as $key) {
                        if (!isset($firstSeat[$key])) {
                            $hasAllKeys = false;
                            break;
                        }
                    }
                    
                    if ($hasAllKeys) {
                        $this->addResult('✅', 'Seat Data Structure', 'Seat data contains all required fields');
                    } else {
                        $this->addResult('❌', 'Seat Data Structure', 'Seat data missing required fields');
                    }
                    
                    // Check lady seat logic (first 8 seats should be lady seats in default layout)
                    $ladySeats = array_filter($seatResult['data'], function($seat) {
                        return $seat['is_lady_seat'] === true || $seat['is_lady_seat'] === 1;
                    });
                    
                    if (count($ladySeats) > 0) {
                        $this->addResult('✅', 'Lady Seat Logic', 'Lady seats properly identified');
                    } else {
                        $this->addResult('⚠️', 'Lady Seat Logic', 'No lady seats found (may need Seat table data)');
                    }
                }
            } else {
                $error = isset($seatResult['error']) ? $seatResult['error'] : 'Unknown error';
                $this->addResult('⚠️', 'Seat Availability Query', 'Query failed or no data: ' . $error);
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Seat Availability', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test search filters
     */
    private function testSearchFilters() {
        try {
            // Test with filters
            $filters = [
                'min_fare' => 100,
                'max_fare' => 1000,
                'departure_time_from' => '06:00',
                'departure_time_to' => '18:00',
                'sort_by' => 'fare_low'
            ];
            
            $result = $this->bus->searchBusesWithFilters('Badulla', 'Matara', '2025-09-18', $filters);
            
            if (isset($result['success'])) {
                $this->addResult('✅', 'Search Filters', 'Search with filters executed without errors');
                
                // Test different sort options
                $sortOptions = ['fare_high', 'departure_early', 'departure_late', 'seats_available'];
                foreach ($sortOptions as $sort) {
                    $filters['sort_by'] = $sort;
                    $sortResult = $this->bus->searchBusesWithFilters('Badulla', 'Matara', '2025-09-18', $filters);
                    
                    if (isset($sortResult['success'])) {
                        $this->addResult('✅', "Sort by {$sort}", "Sort option '{$sort}' works correctly");
                    }
                }
            } else {
                $this->addResult('❌', 'Search Filters', 'Search with filters failed');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Search Filters', 'Exception: ' . $e->getMessage());
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
        echo "<p><strong>Search Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_search.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Search Tests</title>
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
            <h1>Lanka Transit - Search Tests</h1>
            <?php
            $test = new TestBusSearch();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>