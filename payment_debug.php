<?php
/**
 * Payment Debug Tool - Debug payment return issues
 * Access: payment_debug.php?debug_key=lankatransit2025&order_id=ORDER_ID
 */

// Security check
$debug_key = $_GET['debug_key'] ?? '';
$expected_key = 'lankatransit2025';

if ($debug_key !== $expected_key) {
    http_response_code(404);
    exit('Not Found');
}

require_once 'includes/session_config.php';
require_once 'classes/Database.php';
require_once 'classes/Payment.php';

$order_id = $_GET['order_id'] ?? '';

echo "<h2>Payment Debug Tool</h2>";
echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";

if (empty($order_id)) {
    echo "<p style='color: red;'>Please provide order_id parameter</p>";
    echo "<p>Usage: payment_debug.php?debug_key=lankatransit2025&order_id=YOUR_ORDER_ID</p>";
    exit;
}

echo "<h3>Order ID: " . htmlspecialchars($order_id) . "</h3>";

try {
    $payment = new Payment();
    
    // Check session data
    echo "<h4>Session Data</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    echo "<tr><td>Session ID</td><td>" . session_id() . "</td></tr>";
    echo "<tr><td>payment_order_id</td><td>" . ($_SESSION['payment_order_id'] ?? 'Not set') . "</td></tr>";
    echo "<tr><td>Has payment_booking_data</td><td>" . (isset($_SESSION['payment_booking_data']) ? 'Yes' : 'No') . "</td></tr>";
    
    if (isset($_SESSION['payment_booking_data'])) {
        echo "<tr><td>Booking Data</td><td><pre>" . print_r($_SESSION['payment_booking_data'], true) . "</pre></td></tr>";
    }
    echo "</table>";
    
    // Check payment session method
    echo "<h4>Payment Session Check</h4>";
    $sessionData = $payment->getPaymentSession($order_id);
    if ($sessionData) {
        echo "<p style='color: green;'>✓ Payment session data found</p>";
        echo "<pre>" . print_r($sessionData, true) . "</pre>";
    } else {
        echo "<p style='color: red;'>✗ No payment session data found</p>";
    }
    
    // Check database payment status
    echo "<h4>Database Payment Status</h4>";
    $paymentStatus = $payment->getPaymentStatus($order_id);
    if ($paymentStatus) {
        echo "<p style='color: green;'>✓ Payment record found in database</p>";
        echo "<table border='1' cellpadding='5'>";
        foreach ($paymentStatus as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ No payment record found in database</p>";
    }
    
    // Manual verification using existing methods
    echo "<h4>Payment Return Verification (using existing methods)</h4>";
    $hasSession = isset($_SESSION['payment_order_id']) && $_SESSION['payment_order_id'] === $order_id;
    $hasPaymentRecord = !empty($paymentStatus);
    $isSuccess = ($hasSession || $hasPaymentRecord);
    
    echo "<p style='color: " . ($isSuccess ? 'green' : 'red') . ";'>";
    echo ($isSuccess ? '✓' : '✗') . " Payment verification: ";
    if ($hasSession) {
        echo "Session data found";
    } elseif ($hasPaymentRecord) {
        echo "Database record found";
    } else {
        echo "PayHere return URL (assume success)";
    }
    echo "</p>";
    
    // Show GET parameters
    echo "<h4>GET Parameters</h4>";
    if (!empty($_GET)) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Parameter</th><th>Value</th></tr>";
        foreach ($_GET as $key => $value) {
            echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No GET parameters found</p>";
    }
    
    // Database query test
    echo "<h4>Direct Database Query</h4>";
    $database = new Database();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM Payment WHERE TransactionId = ?");
    $stmt->execute([$order_id]);
    $directResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($directResult) {
        echo "<p style='color: green;'>✓ Direct database query found records</p>";
        foreach ($directResult as $record) {
            echo "<pre>" . print_r($record, true) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>✗ Direct database query found no records</p>";
    }
    
    // Test actions
    echo "<h4>Test Actions</h4>";
    echo "<p>";
    echo "<a href='payment_return.php?order_id=" . urlencode($order_id) . "'>Test Payment Return</a> | ";
    echo "<a href='confirmation.php?order_id=" . urlencode($order_id) . "'>Test Confirmation</a> | ";
    
    if (isset($_GET['create_temp_payment'])) {
        // Payment records are created via PayHere notification system only
        echo "<p style='color: orange;'>Note: Payment records are created through PayHere notification system only, not directly.</p>";
    } else {
        echo "<span style='color: gray;'>Payment Record Creation (Not Available - handled by PayHere notifications)</span>";
    }
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><small>Debug URL format: payment_debug.php?debug_key=lankatransit2025&order_id=YOUR_ORDER_ID</small></p>";
?>