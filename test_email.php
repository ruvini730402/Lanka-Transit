<?php
/**
 * Email Configuration Test Script
 * Use this to test email functionality in shared hosting
 */

require_once __DIR__ . '/includes/email_helper.php';

echo "<h2>Lanka Transit Email Configuration Test</h2>\n";
echo "<p>Testing email configuration for shared hosting environment...</p>\n";

// Test 1: Environment Variables
echo "<h3>1. Environment Variables Test</h3>\n";
loadEnvVariables();
$mailHost = env('MAIL_HOST', 'NOT_SET');
$mailUsername = env('MAIL_USERNAME', 'NOT_SET');
$mailFromAddress = env('MAIL_FROM_ADDRESS', 'NOT_SET');

echo "Mail Host: " . htmlspecialchars($mailHost) . "<br>\n";
echo "Mail Username: " . htmlspecialchars($mailUsername) . "<br>\n";
echo "Mail From Address: " . htmlspecialchars($mailFromAddress) . "<br>\n";

if ($mailHost === 'NOT_SET' || $mailUsername === 'NOT_SET') {
    echo "<span style='color: red;'>❌ Environment variables not properly loaded!</span><br>\n";
} else {
    echo "<span style='color: green;'>✅ Environment variables loaded successfully!</span><br>\n";
}

// Test 2: SMTP Connection
echo "<h3>2. SMTP Connection Test</h3>\n";
$smtpTest = testEmailConfiguration();
if ($smtpTest['success']) {
    echo "<span style='color: green;'>✅ SMTP Connection: " . htmlspecialchars($smtpTest['message']) . "</span><br>\n";
} else {
    echo "<span style='color: red;'>❌ SMTP Connection: " . htmlspecialchars($smtpTest['message']) . "</span><br>\n";
}

// Test 3: Send Test Email (uncomment and modify recipient to test)
/*
echo "<h3>3. Test Email Send</h3>\n";
$testEmail = 'your-test-email@example.com'; // Replace with your email
$testResult = sendRegistrationEmail($testEmail, 'Test User');
if ($testResult['success']) {
    echo "<span style='color: green;'>✅ Test Email: " . htmlspecialchars($testResult['message']) . "</span><br>\n";
} else {
    echo "<span style='color: red;'>❌ Test Email: " . htmlspecialchars($testResult['message']) . "</span><br>\n";
}
*/

echo "<h3>Troubleshooting Tips for Shared Hosting:</h3>\n";
echo "<ul>\n";
echo "<li>Ensure your .env file has correct SMTP credentials</li>\n";
echo "<li>Check if your hosting provider blocks outgoing SMTP connections</li>\n";
echo "<li>Try using your hosting provider's SMTP server instead of Gmail</li>\n";
echo "<li>Verify that PHP has the necessary extensions (openssl, mbstring)</li>\n";
echo "<li>Check server error logs for detailed error messages</li>\n";
echo "</ul>\n";

echo "<h3>Current PHP Configuration:</h3>\n";
echo "PHP Version: " . phpversion() . "<br>\n";
echo "OpenSSL Extension: " . (extension_loaded('openssl') ? '✅ Loaded' : '❌ Not Loaded') . "<br>\n";
echo "MBString Extension: " . (extension_loaded('mbstring') ? '✅ Loaded' : '❌ Not Loaded') . "<br>\n";
echo "cURL Extension: " . (extension_loaded('curl') ? '✅ Loaded' : '❌ Not Loaded') . "<br>\n";

// Show recent error log entries related to email
echo "<h3>Recent Email Error Log Entries:</h3>\n";
$errorLog = error_get_last();
if ($errorLog) {
    echo "<pre>" . htmlspecialchars(print_r($errorLog, true)) . "</pre>\n";
} else {
    echo "No recent PHP errors found.<br>\n";
}
?>