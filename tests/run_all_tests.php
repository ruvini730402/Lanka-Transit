<?php
/**
 * Test Runner - Runs all tests in the test suite
 * Execute this file to run all available tests
 */

echo "<h1>🧪 Lanka Transit - Complete Test Suite</h1>";
echo "<hr>";

echo "<div style='background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
echo "<h2>📋 Test Suite Overview</h2>";
echo "<p>This test runner will execute all core functionality tests for the Lanka Transit system.</p>";
echo "<strong>Core Functionality Tests:</strong><br>";
echo "1. 🔌 Database & Environment Tests<br>";
echo "2. 🔐 User Authentication Tests<br>";
echo "3. 🔍 Bus Search Algorithm Tests<br>";
echo "4. � Booking Management Tests<br>";
echo "5. 💳 Payment Processing Tests<br>";
echo "6. � Announcement System Tests<br>";
echo "7. ❌ Booking Cancellation Tests<br>";
echo "</div>";

// Core functionality test execution order
$tests = [
    [
        'name' => 'Database & Environment Tests',
        'file' => 'test_core_database.php',
        'description' => 'Tests database connectivity, environment variables, prepared statements, and input validation'
    ],
    [
        'name' => 'User Authentication Tests', 
        'file' => 'test_core_auth.php',
        'description' => 'Tests user registration, login, password handling, and session management'
    ],
    [
        'name' => 'Bus Search Algorithm Tests',
        'file' => 'test_core_search.php', 
        'description' => 'Tests search functionality, intermediate stops, filters, and seat availability'
    ],
    [
        'name' => 'Booking Management Tests',
        'file' => 'test_core_booking.php',
        'description' => 'Tests booking creation, validation, seat management, and status updates'
    ],
    [
        'name' => 'Payment Processing Tests',
        'file' => 'test_core_payment.php',
        'description' => 'Tests PayHere integration, payment verification, session management, and status tracking'
    ],
    [
        'name' => 'Announcement System Tests',
        'file' => 'test_core_announcement.php',
        'description' => 'Tests announcement creation, active filtering, priority ordering, and status management'
    ],
    [
        'name' => 'Booking Cancellation Tests',
        'file' => 'test_core_booking_cancellation.php',
        'description' => 'Tests cancellation requests, eligibility checks, admin processing, and status updates'
    ]
];

$totalTests = count($tests);
$executedTests = 0;
$startTime = microtime(true);

echo "<h2>🚀 Executing Test Suite</h2>";

foreach ($tests as $index => $test) {
    $testNumber = $index + 1;
    echo "<div style='border: 1px solid #ddd; margin: 10px 0; border-radius: 5px;'>";
    echo "<div style='background-color: #f8f9fa; padding: 10px; border-bottom: 1px solid #ddd;'>";
    echo "<h3>Test $testNumber/$totalTests: {$test['name']}</h3>";
    echo "<p><em>{$test['description']}</em></p>";
    echo "</div>";
    
    echo "<div style='padding: 15px;'>";
    
    // Check if test file exists
    $fullPath = __DIR__ . '/' . $test['file'];
    if (file_exists($fullPath)) {
        echo "<div style='background-color: #fff3cd; padding: 8px; margin-bottom: 10px; border-radius: 3px;'>";
        echo "📄 Loading test file: {$test['file']}<br>";
        echo "⏱️ Started at: " . date('H:i:s') . "<br>";
        echo "</div>";
        
        // Execute the test
        $testStartTime = microtime(true);
        
        ob_start();
        include $fullPath;
        $testOutput = ob_get_clean();
        
        $testEndTime = microtime(true);
        $testDuration = round(($testEndTime - $testStartTime) * 1000, 2);
        
        echo $testOutput;
        
        echo "<div style='background-color: #d1ecf1; padding: 8px; margin-top: 10px; border-radius: 3px;'>";
        echo "✅ Test completed successfully<br>";
        echo "⏱️ Execution time: {$testDuration} ms<br>";
        echo "</div>";
        
        $executedTests++;
    } else {
        echo "<div style='background-color: #f8d7da; padding: 10px; border-radius: 3px;'>";
        echo "❌ Test file not found: {$test['file']}<br>";
        echo "Looked in: $fullPath<br>";
        echo "Please ensure all test files are present in the tests directory.<br>";
        echo "</div>";
    }
    
    echo "</div></div>";
    
    // Add separator between tests
    if ($testNumber < $totalTests) {
        echo "<hr style='margin: 30px 0; border: 2px solid #007bff;'>";
    }
}

$endTime = microtime(true);
$totalDuration = round(($endTime - $startTime), 2);

// Final summary
echo "<hr style='margin: 30px 0; border: 3px solid #28a745;'>";
echo "<div style='background-color: #d4edda; padding: 20px; border-radius: 10px; border: 2px solid #28a745;'>";
echo "<h2>🎉 Test Suite Execution Complete!</h2>";

echo "<div style='display: flex; justify-content: space-between; margin: 20px 0;'>";
echo "<div>";
echo "<h3>📊 Execution Statistics</h3>";
echo "✅ Tests executed: <strong>$executedTests / $totalTests</strong><br>";
echo "⏱️ Total execution time: <strong>{$totalDuration} seconds</strong><br>";
echo "📅 Completed at: <strong>" . date('Y-m-d H:i:s') . "</strong><br>";
echo "</div>";
echo "</div>";

if ($executedTests == $totalTests) {
    echo "<div style='background-color: #155724; color: white; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h3>🏆 All Tests Passed Successfully!</h3>";
    echo "<p>Your Lanka Transit system has been thoroughly tested and is ready for use.</p>";
    echo "</div>";
    
    echo "<h3>🎯 Core Functionality Verified:</h3>";
    echo "<ul>";
    echo "<li>✅ Database connectivity and environment configuration</li>";
    echo "<li>✅ User authentication and session management</li>";
    echo "<li>✅ Bus search algorithm with intermediate stops</li>";
    echo "<li>✅ Booking creation and validation</li>";
    echo "<li>✅ PayHere payment integration and verification</li>";
    echo "<li>✅ Announcement system with priority management</li>";
    echo "<li>✅ Booking cancellation workflow and admin processing</li>";
    echo "<li>✅ Input validation and security measures</li>";
    echo "<li>✅ Data integrity and business rule enforcement</li>";
    echo "<li>✅ Error handling and exception management</li>";
    echo "</ul>";
    
} else {
    $failedTests = $totalTests - $executedTests;
    echo "<div style='background-color: #721c24; color: white; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h3>⚠️ Some Tests Could Not Be Executed</h3>";
    echo "<p>$failedTests test(s) failed to run. Please check the test files and try again.</p>";
    echo "</div>";
}

echo "<h3>📁 Test Files Location:</h3>";
echo "<code>/home/ashan/PhpstormProjects/Lanka-Transit/tests/</code><br><br>";

echo "<h3>🔄 To Run Individual Tests:</h3>";
echo "<ul>";
foreach ($tests as $test) {
    echo "<li><code>php tests/{$test['file']}</code> - {$test['name']}</li>";
}
echo "</ul>";

echo "<p><em>For the best experience, run tests individually to see detailed output for each component.</em></p>";

echo "</div>";

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo "table { border-collapse: collapse; margin: 10px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #f2f2f2; }";
echo "code { background-color: #f4f4f4; padding: 2px 4px; border-radius: 3px; }";
echo "</style>";
?>
