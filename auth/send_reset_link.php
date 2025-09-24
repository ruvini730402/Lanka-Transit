<?php
require_once __DIR__ . '/../includes/session_config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$email = $_POST['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['forgot_message'] = '❌ Invalid email address.';
    header('Location: ../pages/forgot-password.php');
    exit;
}

$user = new User();
$userData = $user->findByEmail($email);
if (!$userData) {
    $_SESSION['forgot_message'] = '❌ Email not found.';
    header('Location: ../pages/forgot-password.php');
    exit;
}

// Generate token and expiry
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Store token and expiry in DB
$user->setResetToken($email, $token, $expiry);

// Send email using PHPMailer
$mail = new PHPMailer(true);
$emailSent = false;
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'lankatransitmailer@gmail.com';
    $mail->Password = 'afdowadfulydoqhv';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email settings
    $mail->setFrom('no-reply@lankatransit.com', 'Lanka Transit');
    $mail->addAddress($email, $userData['Name']);
    $mail->Subject = 'Reset Your Lanka Transit Password';
    $mail->isHTML(true);

    // HTML email body
    $mail->Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
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
                <h1>🔑 Password Reset Request</h1>
            </div>
            <div class="content">
                <h2>Hello ' . htmlspecialchars($userData['Name']) . '! 👋</h2>
                <p>We received a request to reset your password for <span class="highlight">Lanka Transit</span>.</p>
                <p>Click the button below to set a new password. This link is valid for 1 hour:</p>
                <a href="http://localhost/Lanka-Transit/pages/reset_password_form.php?token=' . urlencode($token) . '" class="button">Reset Password Now</a>
                <p>If you didn’t request this, you can safely ignore this email.</p>
            </div>
            <div class="footer">
                <p>Best regards,<br>The Lanka Transit Team 🌟</p>
                <p><a href="http://localhost/Lanka-Transit">Visit our website</a> | <a href="mailto:support@lankatransit.com">Contact Support</a></p>
            </div>
        </div>
    </body>
    </html>';

    // Plain text fallback for email clients that don't support HTML
    $mail->AltBody = "Dear {$userData['Name']},\n\nWe received a request to reset your Lanka Transit password.\n\nClick here to reset your password (valid for 1 hour): http://localhost/Lanka-Transit/pages/reset_password_form.php?token=" . urlencode($token) . "\n\nIf you didn’t request this, please ignore this email.\n\nBest regards,\nLanka Transit Team";

    $emailSent = $mail->send();
} catch (Exception $e) {
    error_log("Failed to send password reset email to $email at " . date('Y-m-d H:i:s') . ": {$mail->ErrorInfo}");
}

if ($emailSent) {
    $_SESSION['success'] = '✅ Reset link has been sent to your email.';
} else {
    $_SESSION['forgot_message'] = '❌ Failed to send email. Please try again.';
}

header('Location: ../pages/forgot-password.php');
exit;
?>