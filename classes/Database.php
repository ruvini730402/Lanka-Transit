<?php
// PHPMailer includes only when needed for email functionality
if (class_exists('PHPMailer\PHPMailer\PHPMailer') === false) {
    // Only include if files exist and PHPMailer is needed
    $phpmailerPath = __DIR__ . '/../PHPMailer/PHPMailer.php';
    if (file_exists($phpmailerPath)) {
        require_once $phpmailerPath;
        require_once __DIR__ . '/../PHPMailer/SMTP.php';
        require_once __DIR__ . '/../PHPMailer/Exception.php';
    }
}

class Database {
    private $host = 'bosennoy016fmb5flv0m-mysql.services.clever-cloud.com';
    private $db_name = 'bosennoy016fmb5flv0m';
    private $username = 'ul9ivik7jhoj9kyh';
    private $password = 'iVbsGABNeLEWyG69bSqj';
    private static $pdo = null;

    // Get PDO database connection
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $instance = new self();
                $dsn = "mysql:host={$instance->host};dbname={$instance->db_name};charset=utf8";
                self::$pdo = new PDO($dsn, $instance->username, $instance->password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("DB Connection failed: " . $e->getMessage());
                die("Database connection failed. Please try again later.");
            }
        }
        return self::$pdo;
    }

    /**
     * Sanitize input to prevent XSS and clean data
     */
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate input based on type
     */
    public static function validateInput($input, $type = 'string') {
        $input = trim($input);
        switch ($type) {
            case 'date':
                return !empty($input) && strtotime($input) !== false;
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            case 'phone':
                return preg_match('/^\d{10}$/', $input);
            case 'string':
            default:
                return !empty($input) && strlen($input) > 0;
        }
    }

    // Send password reset email
    public static function sendResetEmail($email, $resetLink) {
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer not available for email sending");
            return false;
        }
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lankatransitmailer@gmail.com';
            $mail->Password = 'afdowadfulydoqhv';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];
            // Set email details
            $mail->setFrom('lankatransitmailer@gmail.com', 'LankaTransit');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset - LankaTransit';
            $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset Request</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f0f4f8 0%, #e8f0fe 100%);
      color: #333;
    }
    .container {
      max-width: 600px;
      margin: 20px auto;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    .header {
      background: linear-gradient(135deg, #800000 0%, #a30000 100%);
      padding: 20px;
      text-align: center;
    }
    .header img {
      max-width: 150px;
      height: auto;
    }
    .header h1 {
      color: #ffffff;
      font-size: 24px;
      margin: 10px 0 0;
      font-weight: 600;
    }
    .content {
      padding: 30px;
      color: #003366;
    }
    .content h4 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #003366;
    }
    .content p {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background: linear-gradient(135deg, #800000 0%, #a30000 100%);
      color: #ffffff;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: linear-gradient(135deg, #a30000 0%, #cc0000 100%);
      box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
    }
    .footer {
      background: #f9f9f9;
      padding: 20px;
      text-align: center;
      font-size: 14px;
      color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer p {
      margin: 0;
    }
    .highlight {
      color: #800000;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Lanka Transit</h1>
    </div>
    <div class="content">
      <h4>Hello,</h4>
      <p>You have requested a password reset for your <span class="highlight">Lanka Transit</span> account. Please click the button below to reset your password:</p>
      <p style="text-align: center;">
        <a href="' . htmlspecialchars($resetLink) . '" class="btn">Reset Password</a>
      </p>
      <p>This link is valid for <span class="highlight">1 hour</span>. If you did not request a password reset, please ignore this email or contact our support team.</p>
      <p>Thank you for choosing <span class="highlight">Lanka Transit</span>!</p>
    </div>
    <div class="footer">
      <p>&copy; ' . date("Y") . ' Lanka Transit. All rights reserved.</p>
      <p>For support, contact us at <a href="mailto:support@lankatransit.com" style="color: #003366;">support@lankatransit.com</a></p>
    </div>
  </div>
</body>
</html>
';
            $mail->AltBody = "Dear User,\n\nClick the following link to reset your password: $resetLink\n\nThis link will expire in 1 hour.\n\nBest regards,\nLanka Transit";
            error_log("Sending reset email to: $email with link: $resetLink");
            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        } catch (Exception $e) {
            error_log("General Error: " . $e->getMessage());
            return false;
        }
    }

    // Send registration confirmation email
    public static function sendRegistrationEmail($email, $name) {
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer not available for email sending");
            return false;
        }
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lankatransitmailer@gmail.com';
            $mail->Password = 'afdowadfulydoqhv';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];
            // Set email details
            $mail->setFrom('lankatransitmailer@gmail.com', 'LankaTransit');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to LankaTransit!';
            $mail->Body = "
    <div style='font-family: Arial, sans-serif; background-color: #f4f7fa; padding: 20px;'>
        <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;'>
            <div style='background-color: #007bff; color: white; padding: 15px 20px; text-align: center;'>
                <h2 style='margin: 0; font-size: 24px;'>🚍 Welcome to LankaTransit!</h2>
            </div>
            <div style='padding: 20px; color: #333;'>
                <h4 style='font-size: 20px;'>👋 Hello, " . htmlspecialchars($name) . "</h4>
                <p style='font-size: 16px;'>Thank you for registering with <strong>LankaTransit</strong>! 🎉</p>
                <p style='font-size: 15px; color: #555;'>Your account has been successfully created. You can now log in to explore our services and start your journey.</p>
                <p style='text-align: center; margin: 25px 0;'>
                    <a href='http://localhost/Lanka-Transit/pages/login-form.php' style='display: inline-block; padding: 12px 25px; background-color: #28a745; color: white; text-decoration: none; font-size: 16px; border-radius: 5px;'>🔑 Log In Now</a>
                </p>
                <p style='font-size: 14px; color: #555;'>If you have any questions, feel free to contact our support team 💬.</p>
                <p style='margin-top: 30px; font-size: 15px;'>Best regards,<br>💼 <strong>LankaTransit Team</strong></p>
            </div>
            <div style='background-color: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; color: #777;'>
                © " . date('Y') . " LankaTransit. All rights reserved.
            </div>
        </div>
    </div>
";
            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("PHPMailer Error (Registration Email): " . $mail->ErrorInfo);
            return false;
        } catch (Exception $e) {
            error_log("General Error (Registration Email): " . $e->getMessage());
            return false;
        }
    }
}