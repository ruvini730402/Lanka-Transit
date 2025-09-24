<?php
/**
 * Email Helper Functions for Lanka Transit
 * Handles email sending with proper SMTP configuration for shared hosting
 */

require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Load environment variables from .env file
 */
function loadEnvVariables() {
    static $envLoaded = false;
    if ($envLoaded) return;
    
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
    $envLoaded = true;
}

/**
 * Get environment variable with fallback
 */
function env($key, $default = null) {
    loadEnvVariables();
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

/**
 * Configure PHPMailer with proper settings for shared hosting
 */
function configurePHPMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings optimized for shared hosting
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USERNAME', 'lankatransitmailer@gmail.com');
        $mail->Password = env('MAIL_PASSWORD', 'afdowadfulydoqhv');
        
        // Use TLS encryption (more compatible with shared hosting)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)env('MAIL_PORT', 587);
        
        // Additional settings for shared hosting compatibility
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'cafile' => false,
                'capath' => false,
                'local_cert' => false,
                'passphrase' => false,
                'CN_match' => false,
                'verify_depth' => 0,
                'ciphers' => 'DEFAULT',
                'capture_peer_cert' => false
            )
        );
        
        // Timeout and connection settings for shared hosting
        $mail->Timeout = 120; // Longer timeout for shared hosting
        $mail->SMTPKeepAlive = false; // Disable keep-alive for shared hosting
        $mail->SMTPAutoTLS = false; // Disable auto TLS detection
        
        // Set explicit authentication type for shared hosting
        $mail->SMTPSecure = 'tls';
        $mail->AuthType = 'LOGIN';
        
        // Enable verbose debug output for troubleshooting (disable in production)
        if (env('APP_ENV', 'production') === 'development') {
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer Debug ($level): $str");
            };
        }
        
        // Set default from address
        $mail->setFrom(
            env('MAIL_FROM_ADDRESS', 'noreply@lankatransit.com'), 
            env('MAIL_FROM_NAME', 'Lanka Transit')
        );
        
        // Set reply-to
        $mail->addReplyTo(
            env('MAIL_FROM_ADDRESS', 'noreply@lankatransit.com'), 
            env('MAIL_FROM_NAME', 'Lanka Transit')
        );
        
        return $mail;
        
    } catch (Exception $e) {
        error_log("PHPMailer configuration error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Send registration confirmation email with fallback methods for shared hosting
 */
function sendRegistrationEmail($recipientEmail, $recipientName) {
    // Method 1: Try standard SMTP
    $result = sendEmailViaSMTP($recipientEmail, $recipientName);
    if ($result['success']) {
        return $result;
    }
    
    // Method 2: Try with alternative SMTP settings
    $result = sendEmailWithAlternativeSettings($recipientEmail, $recipientName);
    if ($result['success']) {
        return $result;
    }
    
    // Method 3: Try with PHP mail() function as final fallback
    $result = sendEmailViaPhpMail($recipientEmail, $recipientName);
    return $result;
}

/**
 * Primary SMTP email sending method
 */
function sendEmailViaSMTP($recipientEmail, $recipientName) {
    try {
        $mail = configurePHPMailer();
        
        // Recipients
        $mail->addAddress($recipientEmail, $recipientName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = '🚍 Welcome to Lanka Transit!';
        
        // HTML email body
        $mail->Body = getRegistrationEmailHTML($recipientName, $recipientEmail);
        
        // Plain text fallback
        $mail->AltBody = getRegistrationEmailText($recipientName, $recipientEmail);
        
        $result = $mail->send();
        
        if ($result) {
            error_log("Registration email sent successfully via SMTP to: $recipientEmail");
            return ['success' => true, 'message' => 'Email sent successfully via SMTP'];
        } else {
            error_log("Failed to send registration email via SMTP to: $recipientEmail - " . $mail->ErrorInfo);
            return ['success' => false, 'message' => 'SMTP failed: ' . $mail->ErrorInfo];
        }
        
    } catch (Exception $e) {
        $errorMsg = "SMTP email error for $recipientEmail: " . $e->getMessage();
        error_log($errorMsg);
        return ['success' => false, 'message' => $errorMsg];
    }
}

/**
 * Alternative SMTP settings for problematic shared hosting
 */
function sendEmailWithAlternativeSettings($recipientEmail, $recipientName) {
    try {
        $mail = new PHPMailer(true);
        
        // Alternative SMTP settings for shared hosting
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USERNAME', 'lankatransitmailer@gmail.com');
        $mail->Password = env('MAIL_PASSWORD', 'afdowadfulydoqhv');
        
        // Use SSL instead of TLS for some shared hosts
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Minimal SSL options for shared hosting
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = false;
        
        // Set sender
        $mail->setFrom(
            env('MAIL_FROM_ADDRESS', 'noreply@lankatransit.com'), 
            env('MAIL_FROM_NAME', 'Lanka Transit')
        );
        
        // Recipients
        $mail->addAddress($recipientEmail, $recipientName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = '🚍 Welcome to Lanka Transit!';
        $mail->Body = getRegistrationEmailHTML($recipientName, $recipientEmail);
        $mail->AltBody = getRegistrationEmailText($recipientName, $recipientEmail);
        
        $result = $mail->send();
        
        if ($result) {
            error_log("Registration email sent successfully via alternative SMTP to: $recipientEmail");
            return ['success' => true, 'message' => 'Email sent successfully via alternative SMTP'];
        } else {
            return ['success' => false, 'message' => 'Alternative SMTP failed: ' . $mail->ErrorInfo];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Alternative SMTP error: ' . $e->getMessage()];
    }
}

/**
 * Fallback to PHP mail() function for shared hosting
 */
function sendEmailViaPhpMail($recipientEmail, $recipientName) {
    try {
        $subject = '🚍 Welcome to Lanka Transit!';
        $message = getRegistrationEmailText($recipientName, $recipientEmail);
        
        $headers = array();
        $headers[] = 'From: ' . env('MAIL_FROM_NAME', 'Lanka Transit') . ' <' . env('MAIL_FROM_ADDRESS', 'noreply@lankatransit.com') . '>';
        $headers[] = 'Reply-To: ' . env('MAIL_FROM_ADDRESS', 'noreply@lankatransit.com');
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        
        $result = mail($recipientEmail, $subject, $message, implode("\r\n", $headers));
        
        if ($result) {
            error_log("Registration email sent successfully via PHP mail() to: $recipientEmail");
            return ['success' => true, 'message' => 'Email sent successfully via PHP mail()'];
        } else {
            error_log("Failed to send registration email via PHP mail() to: $recipientEmail");
            return ['success' => false, 'message' => 'PHP mail() function failed'];
        }
        
    } catch (Exception $e) {
        $errorMsg = "PHP mail() error for $recipientEmail: " . $e->getMessage();
        error_log($errorMsg);
        return ['success' => false, 'message' => $errorMsg];
    }
}

/**
 * Generate HTML email template for registration
 */
function getRegistrationEmailHTML($name, $email) {
    $baseUrl = env('BASE_URL', 'http://localhost:3000');
    $appName = env('APP_NAME', 'Lanka Transit');
    
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); }
            .header { background: linear-gradient(135deg, #800000, #a30000); padding: 20px; text-align: center; border-radius: 20px 20px 0 0; }
            .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
            .content { padding: 30px; color: #333333; }
            .content h2 { color: #003366; font-size: 20px; }
            .content p { font-size: 16px; line-height: 1.6; margin: 10px 0; }
            .content .highlight { color: #800000; font-weight: 600; }
            .button { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #800000, #a30000); color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 600; margin: 20px 0; }
            .button:hover { background: linear-gradient(135deg, #a30000, #cc0000); }
            .footer { background: #f0f4f8; padding: 15px; text-align: center; font-size: 14px; color: #6c757d; border-radius: 0 0 20px 20px; }
            .footer a { color: #003366; text-decoration: none; }
            .footer a:hover { color: #800000; text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Welcome to ' . htmlspecialchars($appName) . '!</h1>
            </div>
            <div class="content">
                <h2>Hello ' . htmlspecialchars($name) . '! 🎉</h2>
                <p>Thank you for joining <span class="highlight">' . htmlspecialchars($appName) . '</span>! Your account has been successfully created.</p>
                <p>🔐 <strong>Login Details:</strong><br>
                   Email: <span class="highlight">' . htmlspecialchars($email) . '</span><br>
                   Use your password to log in.</p>
                <p>Start exploring our services, book your next bus journey, and enjoy seamless travel across Sri Lanka! 🚌</p>
                <div style="text-align: center;">
                    <a href="' . htmlspecialchars($baseUrl) . '/pages/login-form.php" class="button">Login Now</a>
                </div>
            </div>
            <div class="footer">
                <p>Best regards,<br>The ' . htmlspecialchars($appName) . ' Team 🌟</p>
                <p><a href="' . htmlspecialchars($baseUrl) . '">Visit our website</a> | <a href="mailto:support@lankatransit.com">Contact Support</a></p>
            </div>
        </div>
    </body>
    </html>';
}

/**
 * Generate plain text email template for registration
 */
function getRegistrationEmailText($name, $email) {
    $baseUrl = env('BASE_URL', 'http://localhost:3000');
    $appName = env('APP_NAME', 'Lanka Transit');
    
    return "Dear " . $name . ",\n\n" .
           "Thank you for registering with " . $appName . "! Your account has been successfully created.\n\n" .
           "Login Details:\n" .
           "Email: " . $email . "\n" .
           "Use your password to log in.\n\n" .
           "You can log in at: " . $baseUrl . "/pages/login-form.php\n\n" .
           "Start exploring our services and enjoy seamless travel across Sri Lanka!\n\n" .
           "Best regards,\n" .
           "The " . $appName . " Team";
}

/**
 * Test email configuration
 */
function testEmailConfiguration() {
    try {
        $mail = configurePHPMailer();
        
        // Try to connect to the SMTP server
        if ($mail->smtpConnect()) {
            $mail->smtpClose();
            return ['success' => true, 'message' => 'SMTP connection successful'];
        } else {
            return ['success' => false, 'message' => 'SMTP connection failed'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'SMTP test failed: ' . $e->getMessage()];
    }
}
?>