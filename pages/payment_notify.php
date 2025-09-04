<?php
/**
 * PayHere Payment Notification Handler
 * This file is called by PayHere after payment processing
 */

// Disable output buffering and error display for clean response
ob_start();
error_reporting(0);

require_once '../config/database.php';
require_once '../classes/Payment.php';

try {
    // Verify this is a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method Not Allowed');
    }
    
    // Log the notification for debugging (remove in production)
    error_log("PayHere Notification: " . json_encode($_POST));
    
    // Initialize payment processor
    $payment = new Payment();
    
    // Process the payment notification
    $result = $payment->processPaymentNotification($_POST);
    
    if ($result) {
        // Success response
        http_response_code(200);
        echo "OK";
    } else {
        // Error response
        http_response_code(400);
        echo "FAILED";
    }
    
} catch (Exception $e) {
    // Log error and return failure response
    error_log("Payment notification error: " . $e->getMessage());
    http_response_code(500);
    echo "ERROR";
}

ob_end_flush();
?>
