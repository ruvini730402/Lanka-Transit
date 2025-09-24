<?php
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/session_config.php';
require_once __DIR__ . '/../includes/booking_assignment.php';
require_once __DIR__ . '/../includes/email_helper.php';

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
        
        // Send registration confirmation email using improved email helper
        $emailResult = sendRegistrationEmail($email, $name);
        $emailSent = $emailResult['success'];

        if ($emailSent) {
            $_SESSION['success'] = $message . ' A confirmation email has been sent to your email address. Please log in.';
        } else {
            // Log the specific error for debugging
            error_log("Email sending failed during registration for $email: " . ($emailResult['message'] ?? 'Unknown error'));
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