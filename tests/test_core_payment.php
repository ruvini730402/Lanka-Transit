<?php
/**
 * Test Payment Functionality
 * Core payment functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/Payment.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../config/payhere_config.php';

class TestPayment {
    private $results = [];
    private $payment;
    
    public function __construct() {
        $this->payment = new Payment();
    }
    
    /**
     * Run all payment tests
     */
    public function runAllTests() {
        echo "<h2>💳 Payment Tests</h2>";
        
        $this->testPayHereConfig();
        $this->testPaymentFormGeneration();
        $this->testHashGeneration();
        $this->testPaymentVerification();
        $this->testSessionManagement();
        $this->testPaymentStatus();
        
        $this->displayResults();
    }
    
    /**
     * Test PayHere configuration
     */
    private function testPayHereConfig() {
        try {
            // Test merchant ID retrieval
            $merchantId = PayHereConfig::getMerchantId();
            
            if (!empty($merchantId)) {
                $this->addResult('✅', 'Merchant ID Config', 'Merchant ID retrieved from environment');
            } else {
                $this->addResult('❌', 'Merchant ID Config', 'Merchant ID not found in environment');
            }
            
            // Test merchant secret retrieval
            $merchantSecret = PayHereConfig::getMerchantSecret();
            
            if (!empty($merchantSecret)) {
                $this->addResult('✅', 'Merchant Secret Config', 'Merchant secret retrieved from environment');
            } else {
                $this->addResult('❌', 'Merchant Secret Config', 'Merchant secret not found in environment');
            }
            
            // Test sandbox mode
            $isSandbox = PayHereConfig::isSandbox();
            
            if (is_bool($isSandbox)) {
                $this->addResult('✅', 'Sandbox Mode Config', 'Sandbox mode configured correctly');
            } else {
                $this->addResult('❌', 'Sandbox Mode Config', 'Sandbox mode not configured correctly');
            }
            
            // Test URLs
            $checkoutUrl = PayHereConfig::getCheckoutUrl();
            $returnUrl = PayHereConfig::getReturnUrl();
            $cancelUrl = PayHereConfig::getCancelUrl();
            $notifyUrl = PayHereConfig::getNotifyUrl();
            
            if (filter_var($checkoutUrl, FILTER_VALIDATE_URL) &&
                filter_var($returnUrl, FILTER_VALIDATE_URL) &&
                filter_var($cancelUrl, FILTER_VALIDATE_URL) &&
                filter_var($notifyUrl, FILTER_VALIDATE_URL)) {
                $this->addResult('✅', 'URL Configuration', 'All PayHere URLs configured correctly');
            } else {
                $this->addResult('❌', 'URL Configuration', 'Some PayHere URLs not configured correctly');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'PayHere Config', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test payment form generation
     */
    private function testPaymentFormGeneration() {
        try {
            $bookingData = [
                'fare' => 500.00,
                'origin' => 'Badulla',
                'destination' => 'Matara',
                'passenger_name' => 'Test Passenger',
                'phone' => '0771234567',
                'bus_id' => 1
            ];
            
            $result = $this->payment->generatePaymentForm($bookingData);
            
            if (is_array($result) && isset($result['form_data']) && isset($result['action_url'])) {
                $this->addResult('✅', 'Payment Form Structure', 'Payment form generated with correct structure');
                
                $formData = $result['form_data'];
                $requiredFields = ['merchant_id', 'order_id', 'amount', 'currency', 'hash', 'first_name', 'phone'];
                $hasAllFields = true;
                
                foreach ($requiredFields as $field) {
                    if (!isset($formData[$field]) || empty($formData[$field])) {
                        $hasAllFields = false;
                        break;
                    }
                }
                
                if ($hasAllFields) {
                    $this->addResult('✅', 'Payment Form Fields', 'All required payment fields present');
                } else {
                    $this->addResult('❌', 'Payment Form Fields', 'Missing required payment fields');
                }
                
                // Test amount formatting
                if (is_numeric($formData['amount']) && $formData['amount'] == '500.00') {
                    $this->addResult('✅', 'Amount Formatting', 'Payment amount formatted correctly');
                } else {
                    $this->addResult('❌', 'Amount Formatting', 'Payment amount not formatted correctly');
                }
                
                // Test currency
                if ($formData['currency'] === 'LKR') {
                    $this->addResult('✅', 'Currency Setting', 'Currency set correctly to LKR');
                } else {
                    $this->addResult('❌', 'Currency Setting', 'Currency not set correctly');
                }
                
                // Test order ID format
                if (strpos($formData['order_id'], 'LT-') === 0) {
                    $this->addResult('✅', 'Order ID Format', 'Order ID generated with correct prefix');
                } else {
                    $this->addResult('❌', 'Order ID Format', 'Order ID format incorrect');
                }
            } else {
                $this->addResult('❌', 'Payment Form Structure', 'Payment form not generated correctly');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Payment Form Generation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test hash generation
     */
    private function testHashGeneration() {
        try {
            // Test with known values
            $testData = [
                'order_id' => 'LT-TEST-123',
                'amount' => 500.00
            ];
            
            $hash = PayHereConfig::generateHash($testData);
            
            if (!empty($hash) && ctype_xdigit($hash) && strlen($hash) === 32) {
                $this->addResult('✅', 'Hash Generation', 'Payment hash generated correctly');
                
                // Test consistency - same input should produce same hash
                $hash2 = PayHereConfig::generateHash($testData);
                
                if ($hash === $hash2) {
                    $this->addResult('✅', 'Hash Consistency', 'Hash generation is consistent');
                } else {
                    $this->addResult('❌', 'Hash Consistency', 'Hash generation is not consistent');
                }
            } else {
                $this->addResult('❌', 'Hash Generation', 'Hash not generated correctly');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Hash Generation', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test payment verification
     */
    private function testPaymentVerification() {
        try {
            // Mock PayHere notification data
            $merchantId = PayHereConfig::getMerchantId();
            $orderId = 'LT-TEST-' . time();
            $amount = '500.00';
            $currency = 'LKR';
            $statusCode = '2';
            
            // Generate correct signature
            $correctSignature = strtoupper(
                md5(
                    $merchantId . 
                    $orderId . 
                    $amount . 
                    $currency . 
                    $statusCode . 
                    strtoupper(md5(PayHereConfig::getMerchantSecret()))
                )
            );
            
            $notificationData = [
                'merchant_id' => $merchantId,
                'order_id' => $orderId,
                'payhere_amount' => $amount,
                'payhere_currency' => $currency,
                'status_code' => $statusCode,
                'md5sig' => $correctSignature
            ];
            
            $verificationResult = $this->payment->verifyPayment($notificationData);
            
            if ($verificationResult === true) {
                $this->addResult('✅', 'Payment Verification (Valid)', 'Valid payment notification verified correctly');
            } else {
                $this->addResult('❌', 'Payment Verification (Valid)', 'Valid payment notification not verified');
            }
            
            // Test with invalid signature
            $invalidNotificationData = $notificationData;
            $invalidNotificationData['md5sig'] = 'invalid_signature';
            
            $invalidVerificationResult = $this->payment->verifyPayment($invalidNotificationData);
            
            if ($invalidVerificationResult === false) {
                $this->addResult('✅', 'Payment Verification (Invalid)', 'Invalid payment notification correctly rejected');
            } else {
                $this->addResult('❌', 'Payment Verification (Invalid)', 'Invalid payment notification incorrectly accepted');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Payment Verification', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test session management
     */
    private function testSessionManagement() {
        try {
            $orderId = 'LT-TEST-SESSION-' . time();
            $bookingData = [
                'passenger_name' => 'Test User',
                'phone' => '0771234567',
                'fare' => 500
            ];
            
            // Test storing session
            $this->payment->storePaymentSession($orderId, $bookingData);
            
            // Test retrieving session
            $retrievedData = $this->payment->getPaymentSession($orderId);
            
            if ($retrievedData && is_array($retrievedData)) {
                $this->addResult('✅', 'Session Storage', 'Payment session stored and retrieved correctly');
                
                if ($retrievedData['passenger_name'] === $bookingData['passenger_name'] &&
                    $retrievedData['phone'] === $bookingData['phone'] &&
                    $retrievedData['fare'] === $bookingData['fare']) {
                    $this->addResult('✅', 'Session Data Integrity', 'Session data integrity maintained');
                } else {
                    $this->addResult('❌', 'Session Data Integrity', 'Session data corrupted');
                }
            } else {
                $this->addResult('❌', 'Session Storage', 'Payment session not stored/retrieved correctly');
            }
            
            // Test session clearing
            $this->payment->clearPaymentSession();
            $clearedData = $this->payment->getPaymentSession($orderId);
            
            if (!$clearedData) {
                $this->addResult('✅', 'Session Clearing', 'Payment session cleared correctly');
            } else {
                $this->addResult('❌', 'Session Clearing', 'Payment session not cleared');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Session Management', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test payment status methods
     */
    private function testPaymentStatus() {
        try {
            // Note: Payment creation and direct status updates are handled by PayHere notification system
            // These methods are not available in the authorized Payment class specification
            $this->addResult('⚠️', 'Payment Creation Test', 'Skipped - Payment records created via PayHere notifications only');
            $this->addResult('⚠️', 'Payment Status Update Test', 'Skipped - Status updates handled by PayHere notifications only');
            
            // Test authorized method: getPaymentStatus
            $testOrderId = 'TEST-ORDER-' . time();
            $paymentStatus = $this->payment->getPaymentStatus($testOrderId);
            
            if ($paymentStatus === false) {
                $this->addResult('✅', 'Get Payment Status (Non-existent)', 'Correctly returned false for non-existent payment');
            } else {
                $this->addResult('❌', 'Get Payment Status (Non-existent)', 'Should return false for non-existent payment');
            }
            
        } catch (Exception $e) {
            $this->addResult('❌', 'Payment Status', 'Exception: ' . $e->getMessage());
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
        echo "<p><strong>Payment Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_payment.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Payment Tests</title>
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
            <h1>Lanka Transit - Payment Tests</h1>
            <?php
            $test = new TestPayment();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>