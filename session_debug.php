<?php
/**
 * Session Diagnostic Tool for Lanka Transit
 * Use this to diagnose session issues in production
 */

require_once 'includes/session_config.php';

// Security check - only run in specific conditions
$allowed_ips = ['127.0.0.1', '::1']; // Add your IP here for production debugging
$is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowed_ips);
$debug_key = $_GET['debug_key'] ?? '';
$expected_key = 'lankatransit2025'; // Change this for security

if (!$is_local && $debug_key !== $expected_key) {
    http_response_code(404);
    exit('Not Found');
}

echo "<h2>Lanka Transit - Session Diagnostic Tool</h2>";
echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Session Information
echo "<h3>Session Configuration</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Session ID</td><td>" . session_id() . "</td></tr>";
echo "<tr><td>Session Status</td><td>" . session_status() . " (1=disabled, 2=active, 3=none)</td></tr>";
echo "<tr><td>Session Name</td><td>" . session_name() . "</td></tr>";
echo "<tr><td>Session Save Path</td><td>" . session_save_path() . "</td></tr>";
echo "<tr><td>Session Cookie Lifetime</td><td>" . ini_get('session.cookie_lifetime') . "</td></tr>";
echo "<tr><td>Session GC Maxlifetime</td><td>" . ini_get('session.gc_maxlifetime') . "</td></tr>";
echo "</table>";

// PHP Environment
echo "<h3>PHP Environment</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Server Software</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td>Script Filename</td><td>" . (__FILE__) . "</td></tr>";
echo "</table>";

// Session Data
echo "<h3>Current Session Data</h3>";
if (empty($_SESSION)) {
    echo "<p style='color: red;'>No session data found.</p>";
} else {
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

// Session save path check
echo "<h3>Session Directory Check</h3>";
$session_path = session_save_path();
if (empty($session_path)) {
    echo "<p style='color: orange;'>Session save path is not set (using system default).</p>";
} else {
    echo "<p><strong>Session Path:</strong> $session_path</p>";
    
    if (is_dir($session_path)) {
        echo "<p style='color: green;'>✓ Directory exists</p>";
        
        if (is_writable($session_path)) {
            echo "<p style='color: green;'>✓ Directory is writable</p>";
        } else {
            echo "<p style='color: red;'>✗ Directory is not writable</p>";
        }
        
        // List session files
        $files = glob($session_path . '/sess_*');
        echo "<p><strong>Session files:</strong> " . count($files) . "</p>";
        
        if (count($files) > 0) {
            echo "<ul>";
            foreach (array_slice($files, 0, 5) as $file) {
                $mtime = filemtime($file);
                echo "<li>" . basename($file) . " (modified: " . date('Y-m-d H:i:s', $mtime) . ")</li>";
            }
            if (count($files) > 5) {
                echo "<li>... and " . (count($files) - 5) . " more files</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>";
    }
}

// Test session functionality
echo "<h3>Session Functionality Test</h3>";

if (!isset($_SESSION['test_counter'])) {
    $_SESSION['test_counter'] = 1;
    echo "<p style='color: green;'>✓ Session write test: Successfully set test_counter = 1</p>";
} else {
    $_SESSION['test_counter']++;
    echo "<p style='color: green;'>✓ Session read/write test: test_counter = " . $_SESSION['test_counter'] . "</p>";
}

// Add test booking data
if (isset($_GET['add_test_data'])) {
    $_SESSION['booking_data'] = [
        'passenger_name' => 'Test User',
        'phone' => '0771234567',
        'gender' => 'male',
        'nic' => '',
        'origin' => 'Badulla',
        'destination' => 'Matara',
        'travel_date' => date('Y-m-d'),
        'bus_id' => 1,
        'bus_number' => 'TEST-001',
        'seat_number' => '15',
        'fare' => 580.00,
        'departure_time' => '06:00:00',
        'arrival_time' => '12:30:00'
    ];
    echo "<p style='color: blue;'>Test booking data added to session.</p>";
}

// Clear session
if (isset($_GET['clear_session'])) {
    session_destroy();
    echo "<p style='color: orange;'>Session cleared. <a href='?debug_key=$debug_key'>Refresh</a></p>";
}

echo "<hr>";
echo "<h3>Quick Actions</h3>";
echo "<p>";
echo "<a href='?debug_key=$debug_key&add_test_data=1'>Add Test Booking Data</a> | ";
echo "<a href='?debug_key=$debug_key&clear_session=1'>Clear Session</a> | ";
echo "<a href='pages/payment.php'>Test Payment Page</a>";
echo "</p>";

echo "<hr>";
echo "<p><small>Debug URL: ?debug_key=$expected_key</small></p>";
?>