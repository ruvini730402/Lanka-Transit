<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

class Database {
    private static $pdo = null;

    // Get PDO database connection
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO("mysql:host=localhost;dbname=busbooking;charset=utf8", "root", "");
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("DB Connection failed: " . $e->getMessage());
                die("Database connection failed. Please try again later.");
            }
        }
        return self::$pdo;
    }

    // Send password reset email
    public static function sendResetEmail($email, $token) {
        $config=require 'config.php';
        $mail = new PHPMailer(true);

        try {
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_user'];      // ✅ your Gmail
            $mail->Password =  $config['smtp_pass'];             // ✅ 16-digit App Password
            $mail->SMTPSecure = $config['smtp_secure'];;
            $mail->Port =$config['smtp_port']; ;
            
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

        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
