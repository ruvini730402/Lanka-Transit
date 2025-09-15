<?php
/**
 * Test Script for Payment Failure Scenario
 * This script simulates a failed or pending payment return from PayHere
 */
session_start();

// Clear any existing sessions
session_destroy();
session_start();

// Simulate failed payment scenarios
$test_scenarios = [
    'no_order_id' => [
        'description' => 'No Order ID provided',
        'setup' => function() {
            // Don't set any order ID
            return '';
        }
    ],
    'order_not_found' => [
        'description' => 'Order ID provided but no payment record found',
        'setup' => function() {
            $order_id = 'LT-NOTFOUND-' . time();
            $_SESSION['payment_order_id'] = $order_id;
            return $order_id;
        }
    ],
    'payment_pending' => [
        'description' => 'Payment is still processing',
        'setup' => function() {
            $order_id = 'LT-PENDING-' . time();
            $_SESSION['payment_order_id'] = $order_id;
            
            // Create pending payment record
            require_once '../classes/Database.php';
            $db = new Database();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("
                INSERT INTO Payment (OrderID, Amount, Currency, Status, PaymentMethod, PaymentDate) 
                VALUES (?, 1200.00, 'LKR', 'pending', 'PayHere', NOW())
            ");
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            
            return $order_id;
        }
    ],
    'payment_failed' => [
        'description' => 'Payment explicitly failed',
        'setup' => function() {
            $order_id = 'LT-FAILED-' . time();
            $_SESSION['payment_order_id'] = $order_id;
            
            // Create failed payment record
            require_once '../classes/Database.php';
            $db = new Database();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("
                INSERT INTO Payment (OrderID, Amount, Currency, Status, PaymentMethod, PaymentDate) 
                VALUES (?, 1800.00, 'LKR', 'failed', 'PayHere', NOW())
            ");
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            
            return $order_id;
        }
    ]
];

$selected_scenario = $_GET['scenario'] ?? 'no_order_id';

if (!isset($test_scenarios[$selected_scenario])) {
    $selected_scenario = 'no_order_id';
}

echo "<h2>Payment Failure Test - " . $test_scenarios[$selected_scenario]['description'] . "</h2>";

try {
    $test_order_id = $test_scenarios[$selected_scenario]['setup']();
    
    echo "<div style='margin: 20px 0; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h3>Test Scenario Setup:</h3>";
    echo "<p><strong>Scenario:</strong> " . $test_scenarios[$selected_scenario]['description'] . "</p>";
    echo "<p><strong>Test Order ID:</strong> " . ($test_order_id ?: 'None') . "</p>";
    echo "<p><strong>Expected Result:</strong> Payment processing/error page</p>";
    echo "</div>";
    
    $test_url = $test_order_id 
        ? "../pages/payment_return.php?order_id={$test_order_id}"
        : "../pages/payment_return.php";
        
    echo "<div style='margin: 20px 0; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 5px;'>";
    echo "<strong>Test URL:</strong><br>";
    echo "<a href='{$test_url}' target='_blank' style='display: inline-block; margin: 5px 10px 5px 0; padding: 8px 15px; background: #ffc107; color: #212529; text-decoration: none; border-radius: 3px;'>Test Payment Failure Page</a>";
    echo "</div>";
    
    echo "<h3>Expected Behavior:</h3>";
    echo "<ul>";
    echo "<li>❌ Shows 'Processing Payment' or error message</li>";
    echo "<li>⏳ Displays loading spinner</li>";
    echo "<li>🔄 Auto-refreshes every 5 seconds</li>";
    echo "<li>🏠 Shows 'Go Home' button option</li>";
    echo "<li>🔄 Shows 'Refresh Status' button</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<strong>Setup Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Failure Tests</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .scenario-selector { background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .scenario-link { 
            display: inline-block; 
            margin: 5px; 
            padding: 10px 15px; 
            background: #6c757d; 
            color: white; 
            text-decoration: none; 
            border-radius: 3px; 
        }
        .scenario-link:hover { background: #5a6268; }
        .scenario-link.active { background: #dc3545; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <h1>Payment Failure Test Scenarios</h1>
    
    <div class="scenario-selector">
        <h3>Select Test Scenario:</h3>
        <?php foreach ($test_scenarios as $key => $scenario): ?>
            <a href="?scenario=<?php echo $key; ?>" 
               class="scenario-link <?php echo $selected_scenario === $key ? 'active' : ''; ?>">
                <?php echo $scenario['description']; ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <div class="warning">
        <h4>⚠️ Test Instructions:</h4>
        <ol>
            <li>Select a failure scenario above</li>
            <li>Click the "Test Payment Failure Page" button</li>
            <li>Observe the payment return page behavior</li>
            <li>Verify it shows appropriate error/processing states</li>
            <li>Check that auto-refresh works (should refresh every 5 seconds)</li>
        </ol>
    </div>
    
    <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h4>Test Scenarios Explained:</h4>
        <ul>
            <li><strong>No Order ID:</strong> Simulates user arriving without proper payment reference</li>
            <li><strong>Order Not Found:</strong> Order ID exists but no payment record in database</li>
            <li><strong>Payment Pending:</strong> Payment record exists but status is still 'pending'</li>
            <li><strong>Payment Failed:</strong> Payment record exists with 'failed' status</li>
        </ul>
    </div>
</body>
</html>
