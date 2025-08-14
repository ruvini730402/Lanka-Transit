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
    public static function sendResetEmail($email, $token) {
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
            $mail->Body = "
                <h4>Hello,</h4>
                <p>You requested a password reset. Click the link below:</p>
                <a href='http://localhost/Registerlog/pages/reset_password_form.php?token=$token'>Reset Password</a>
                <p>This link is valid for 1 hour.</p>
            ";

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
                <h4>Hello, " . htmlspecialchars($name) . "</h4>
                <p>Thank you for registering with LankaTransit!</p>
                <p>Your account has been successfully created. You can now log in to explore our services.</p>
                <p><a href='http://localhost/Registerlog/pages/login-form.php'>Click here to log in</a></p>
                <p>If you have any questions, feel free to contact our support team.</p>
                <p>Best regards,<br>LankaTransit Team</p>
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