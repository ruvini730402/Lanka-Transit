<?php
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/session_config.php';
require_once __DIR__ . '/../includes/booking_assignment.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $mobile = trim($_POST['mobile'] ?? '');

    // Basic validation for mobile number
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $_SESSION['error'] = 'Please enter a valid 10-digit mobile number.';
        header('Location: ../pages/register-form.php');
        exit();
    }

    $user = new User();
    $result = $user->register($name, $email, $password, $mobile);

    if ($result['success']) {
        // Get the newly created user ID by finding the user with the email
        $newUser = $user->findByEmail($email);
        if ($newUser) {
            $newUserId = $newUser['ID'];
            
            // Assign existing bookings with matching phone number to this user
            $bookingAssignment = assignExistingBookings($newUserId, $mobile);
            
            // Create success message including booking assignment information
            $message = 'Registered successfully!';
            if ($bookingAssignment['success'] && $bookingAssignment['assignedCount'] > 0) {
                $message .= ' ' . $bookingAssignment['message'];
            }
        } else {
            $message = 'Registered successfully!';
        }
        
        // Send registration confirmation email using PHPMailer
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
            $mail->addAddress($email, $name);
            $mail->Subject = '🚍 Welcome to Lanka Transit!';
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
                        <h1>Welcome to Lanka Transit!</h1>
                    </div>
                    <div class="content">
                        <h2>Hello ' . htmlspecialchars($name) . '! 🎉</h2>
                        <p>Thank you for joining <span class="highlight">Lanka Transit</span>! Your account has been successfully created.</p>
                        <p>🔐 <strong>Login Details:</strong><br>
                           Email: <span class="highlight">' . htmlspecialchars($email) . '</span><br>
                           Use your password to log in.</p>
                        <p>Start exploring our services, book your next bus journey, and enjoy seamless travel across Sri Lanka! 🚌</p>
                    </div>
                    <div class="footer">
                        <p>Best regards,<br>The Lanka Transit Team 🌟</p>
                        <p><a href="https://yourwebsite.com">Visit our website</a> | <a href="mailto:support@lankatransit.com">Contact Support</a></p>
                    </div>
                </div>
            </body>
            </html>';

            // Plain text fallback for email clients that don't support HTML
            $mail->AltBody = "Dear $name,\n\nThank you for registering with Lanka Transit! Your account has been successfully created.\n\nYou can now log in using your email ($email) and password at https://yourwebsite.com/pages/login-form.php.\n\nBest regards,\nLanka Transit Team";

            $emailSent = $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send registration email to $email at " . date('Y-m-d H:i:s') . ": {$mail->ErrorInfo}");
        }

        if ($emailSent) {
            $_SESSION['success'] = $message . ' A confirmation email has been sent to your email address. Please log in.';
        } else {
            $_SESSION['success'] = $message . ' However, we could not send a confirmation email due to server configuration. Please log in.';
        }
        
        header('Location: ../pages/login-form.php');
        exit();
    }
    
    $_SESSION['error'] = $result['message'];
    header('Location: ../pages/register-form.php');
    exit();
}
?>