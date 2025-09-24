<?php
/**
 * Advanced Email Debugging for Shared Hosting
 * This script helps identify email configuration issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/email_helper.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanka Transit Email Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; font-weight: bold; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; white-space: pre-wrap; }
        h2 { color: #800000; border-bottom: 2px solid #800000; padding-bottom: 5px; }
        h3 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lanka Transit Email Configuration Debug</h1>
        <p>This diagnostic tool will help identify email configuration issues in your shared hosting environment.</p>

        <?php
        // Test 1: PHP Configuration
        echo "<div class='section'>";
        echo "<h2>1. PHP Configuration Check</h2>";
        
        echo "<h3>PHP Version & Extensions</h3>";
        echo "PHP Version: <span class='info'>" . phpversion() . "</span><br>";
        
        $extensions = ['openssl', 'mbstring', 'curl', 'imap'];
        foreach ($extensions as $ext) {
            $loaded = extension_loaded($ext);
            $status = $loaded ? "<span class='success'>✅ Loaded</span>" : "<span class='error'>❌ Not Loaded</span>";
            echo "$ext: $status<br>";
        }
        
        echo "<h3>Mail Function Availability</h3>";
        if (function_exists('mail')) {
            echo "<span class='success'>✅ PHP mail() function is available</span><br>";
        } else {
            echo "<span class='error'>❌ PHP mail() function is not available</span><br>";
        }
        
        echo "<h3>SMTP Settings from php.ini</h3>";
        echo "SMTP: " . (ini_get('SMTP') ?: 'Not set') . "<br>";
        echo "smtp_port: " . (ini_get('smtp_port') ?: 'Not set') . "<br>";
        echo "sendmail_from: " . (ini_get('sendmail_from') ?: 'Not set') . "<br>";
        echo "</div>";

        // Test 2: Environment Variables
        echo "<div class='section'>";
        echo "<h2>2. Environment Variables Check</h2>";
        
        loadEnvVariables();
        
        $envVars = [
            'MAIL_HOST' => env('MAIL_HOST'),
            'MAIL_PORT' => env('MAIL_PORT'),
            'MAIL_USERNAME' => env('MAIL_USERNAME'),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD') ? '***HIDDEN***' : 'NOT_SET',
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME')
        ];
        
        foreach ($envVars as $key => $value) {
            $status = ($value && $value !== 'NOT_SET') ? 'success' : 'error';
            echo "$key: <span class='$status'>$value</span><br>";
        }
        echo "</div>";

        // Test 3: Network Connectivity
        echo "<div class='section'>";
        echo "<h2>3. Network Connectivity Test</h2>";
        
        $smtpHost = env('MAIL_HOST', 'smtp.gmail.com');
        $smtpPort = env('MAIL_PORT', 587);
        
        echo "<h3>Testing connection to $smtpHost:$smtpPort</h3>";
        
        // Test socket connection
        $connection = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
        if ($connection) {
            echo "<span class='success'>✅ Successfully connected to SMTP server</span><br>";
            fclose($connection);
        } else {
            echo "<span class='error'>❌ Failed to connect to SMTP server</span><br>";
            echo "Error: $errno - $errstr<br>";
        }
        
        // Test alternative port (465 for SSL)
        echo "<h3>Testing connection to $smtpHost:465 (SSL)</h3>";
        $connection = @fsockopen($smtpHost, 465, $errno, $errstr, 10);
        if ($connection) {
            echo "<span class='success'>✅ Successfully connected to SMTP server on SSL port</span><br>";
            fclose($connection);
        } else {
            echo "<span class='error'>❌ Failed to connect to SMTP server on SSL port</span><br>";
            echo "Error: $errno - $errstr<br>";
        }
        echo "</div>";

        // Test 4: PHPMailer SMTP Test
        echo "<div class='section'>";
        echo "<h2>4. PHPMailer SMTP Connection Test</h2>";
        
        $smtpTest = testEmailConfiguration();
        if ($smtpTest['success']) {
            echo "<span class='success'>✅ " . htmlspecialchars($smtpTest['message']) . "</span><br>";
        } else {
            echo "<span class='error'>❌ " . htmlspecialchars($smtpTest['message']) . "</span><br>";
        }
        echo "</div>";

        // Test 5: Send Test Email
        if (isset($_POST['test_email']) && !empty($_POST['recipient_email'])) {
            echo "<div class='section'>";
            echo "<h2>5. Test Email Send Result</h2>";
            
            $testEmail = filter_var($_POST['recipient_email'], FILTER_VALIDATE_EMAIL);
            if ($testEmail) {
                $result = sendRegistrationEmail($testEmail, 'Test User');
                
                if ($result['success']) {
                    echo "<span class='success'>✅ Test email sent successfully!</span><br>";
                    echo "Message: " . htmlspecialchars($result['message']) . "<br>";
                } else {
                    echo "<span class='error'>❌ Test email failed</span><br>";
                    echo "Error: " . htmlspecialchars($result['message']) . "<br>";
                }
            } else {
                echo "<span class='error'>❌ Invalid email address provided</span><br>";
            }
            echo "</div>";
        }
        ?>

        <!-- Test Email Form -->
        <div class='section'>
            <h2>5. Send Test Email</h2>
            <form method="POST">
                <label for="recipient_email">Enter your email address to receive a test email:</label><br>
                <input type="email" name="recipient_email" id="recipient_email" required style="width: 300px; padding: 8px; margin: 10px 0;">
                <br>
                <button type="submit" name="test_email" style="padding: 10px 20px; background: #800000; color: white; border: none; border-radius: 4px; cursor: pointer;">Send Test Email</button>
            </form>
        </div>

        <!-- Troubleshooting Guide -->
        <div class='section'>
            <h2>Troubleshooting Guide for Shared Hosting</h2>
            
            <h3>Common Issues and Solutions:</h3>
            <ul>
                <li><strong>SMTP Connection Failed:</strong> Your hosting provider may block outgoing SMTP connections. Contact support or use your hosting provider's SMTP server.</li>
                <li><strong>SSL/TLS Errors:</strong> Try using port 465 with SSL instead of port 587 with TLS.</li>
                <li><strong>Authentication Failed:</strong> Ensure your Gmail account has "Less secure app access" enabled or use an App Password.</li>
                <li><strong>PHP mail() Function:</strong> Some shared hosts disable the mail() function. Check with your provider.</li>
                <li><strong>Firewall Issues:</strong> Your server's firewall may block outgoing email ports (25, 587, 465).</li>
            </ul>
            
            <h3>Alternative Solutions:</h3>
            <ul>
                <li>Use your hosting provider's SMTP server instead of Gmail</li>
                <li>Use a transactional email service like SendGrid, Mailgun, or Amazon SES</li>
                <li>Enable "Allow less secure apps" in your Gmail account settings</li>
                <li>Use Gmail App Passwords instead of your regular password</li>
                <li>Contact your hosting provider about email configuration</li>
            </ul>
            
            <h3>Hosting Provider SMTP Settings:</h3>
            <div class='code'>
For cPanel shared hosting, try these settings:
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls

Or for some providers:
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
            </div>
        </div>

        <!-- Server Information -->
        <div class='section'>
            <h2>Server Information</h2>
            <div class='code'>
Server Software: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
PHP SAPI: <?php echo php_sapi_name(); ?>
Operating System: <?php echo PHP_OS; ?>
Server Name: <?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?>
Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?>
            </div>
        </div>
    </div>
</body>
</html>