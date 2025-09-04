<?php
/**
 * Payment Class for Lanka Transit
 * Handles PayHere IPG integration and payment processing
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/payhere_config.php';

class Payment {
    private $pdo;
    private $merchant_id;
    private $merchant_secret;
    private $sandbox_mode;
    
    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $database = new Database();
            $this->pdo = $database->getConnection();
        }
        
        // Use configuration from PayHereConfig
        $this->merchant_id = PayHereConfig::MERCHANT_ID;
        $this->merchant_secret = PayHereConfig::MERCHANT_SECRET;
        $this->sandbox_mode = PayHereConfig::SANDBOX_MODE;
    }
    
    /**
     * Generate PayHere payment form data
     */
    public function generatePaymentForm($bookingData) {
        $order_id = 'LT-' . time() . '-' . rand(1000, 9999);
        $amount = number_format($bookingData['fare'], 2, '.', '');
        $currency = 'LKR';
        
        // Generate hash
        $hash = $this->generateHash($order_id, $amount, $currency);
        
        // Get base URL for callbacks
        $base_url = PayHereConfig::BASE_URL;
        
        $formData = [
            'merchant_id' => $this->merchant_id,
            'return_url' => PayHereConfig::getReturnUrl(),
            'cancel_url' => PayHereConfig::getCancelUrl(),
            'notify_url' => PayHereConfig::getNotifyUrl(),
            'order_id' => $order_id,
            'items' => 'Bus Ticket - ' . $bookingData['origin'] . ' to ' . $bookingData['destination'],
            'currency' => PayHereConfig::CURRENCY,
            'amount' => $amount,
            'first_name' => $bookingData['passenger_name'],
            'last_name' => '',
            'email' => $this->generateTempEmail($bookingData['passenger_name']),
            'phone' => $bookingData['phone'],
            'address' => $bookingData['origin'],
            'city' => $bookingData['origin'],
            'country' => 'Sri Lanka',
            'hash' => $hash,
            'custom_1' => $order_id, // Store order_id for reference
            'custom_2' => $bookingData['bus_id']
        ];
        
        return [
            'form_data' => $formData,
            'action_url' => PayHereConfig::getCheckoutUrl(),
            'order_id' => $order_id
        ];
    }
    
    /**
     * Generate PayHere hash
     */
    private function generateHash($order_id, $amount, $currency) {
        return strtoupper(
            md5(
                $this->merchant_id . 
                $order_id . 
                $amount . 
                $currency . 
                strtoupper(md5($this->merchant_secret))
            )
        );
    }
    
    /**
     * Verify payment notification
     */
    public function verifyPayment($notification_data) {
        $merchant_id = $notification_data['merchant_id'] ?? '';
        $order_id = $notification_data['order_id'] ?? '';
        $payhere_amount = $notification_data['payhere_amount'] ?? '';
        $payhere_currency = $notification_data['payhere_currency'] ?? '';
        $status_code = $notification_data['status_code'] ?? '';
        $md5sig = $notification_data['md5sig'] ?? '';
        
        // Generate local signature
        $local_md5sig = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $payhere_amount . 
                $payhere_currency . 
                $status_code . 
                strtoupper(md5($this->merchant_secret))
            )
        );
        
        return ($local_md5sig === $md5sig);
    }
    
    /**
     * Process payment notification
     */
    public function processPaymentNotification($notification_data) {
        if (!$this->verifyPayment($notification_data)) {
            error_log("Payment verification failed: " . json_encode($notification_data));
            return false;
        }
        
        $order_id = $notification_data['order_id'];
        $status_code = (int)$notification_data['status_code'];
        $payhere_amount = $notification_data['payhere_amount'];
        $payment_id = $notification_data['payment_id'] ?? '';
        $method = $notification_data['method'] ?? '';
        
        try {
            $this->pdo->beginTransaction();
            
            $payment_status = $this->getPaymentStatusString($status_code);
            $booking_id = $this->getBookingIdByOrderId($order_id);
            
            if (!$booking_id) {
                // No existing booking, create a placeholder payment record
                // The booking will be created later in confirmation.php
                $stmt = $this->pdo->prepare("
                    INSERT INTO Payment (BookingId, PaymentMethod, Status, Amount, TransactionId, PaymentDate) 
                    VALUES (NULL, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $method,
                    $payment_status,
                    $payhere_amount,
                    $payment_id
                ]);
            } else {
                // Update existing payment record
                $stmt = $this->pdo->prepare("
                    UPDATE Payment 
                    SET Status = ?, TransactionId = ?, PaymentDate = NOW(), PaymentMethod = ?
                    WHERE BookingId = ?
                ");
                $stmt->execute([
                    $payment_status,
                    $payment_id,
                    $method,
                    $booking_id
                ]);
                
                // Update booking status based on payment status
                if ($status_code == 2) { // Success
                    $this->updateBookingStatus($booking_id, 'confirmed');
                } else {
                    $this->updateBookingStatus($booking_id, 'cancelled');
                }
            }
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Payment processing error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get payment status string from status code
     */
    private function getPaymentStatusString($status_code) {
        switch ($status_code) {
            case 2: return 'success';
            case 0: return 'pending';
            case -1: return 'failed';
            case -2: return 'failed';
            case -3: return 'failed';
            default: return 'failed';
        }
    }
    
    /**
     * Update booking status
     */
    private function updateBookingStatus($booking_id, $status) {
        $stmt = $this->pdo->prepare("UPDATE Booking SET Status = ? WHERE ID = ?");
        return $stmt->execute([$status, $booking_id]);
    }
    
    /**
     * Get booking ID by order ID
     */
    private function getBookingIdByOrderId($order_id) {
        // For new payments, we might not have a booking yet
        // We'll store order_id in custom_1 field and match it
        $stmt = $this->pdo->prepare("
            SELECT p.BookingId FROM Payment p 
            WHERE p.TransactionId = ?
            LIMIT 1
        ");
        $stmt->execute([$order_id]);
        $result = $stmt->fetchColumn();
        
        // If no booking found, create a temporary record to link payment
        if (!$result) {
            // This is a new payment notification, we'll handle booking creation in confirmation
            return null;
        }
        
        return $result;
    }
    
    /**
     * Store payment session data
     */
    public function storePaymentSession($order_id, $booking_data) {
        $_SESSION['payment_order_id'] = $order_id;
        $_SESSION['payment_booking_data'] = $booking_data;
    }
    
    /**
     * Get payment session data
     */
    public function getPaymentSession($order_id = null) {
        if ($order_id && isset($_SESSION['payment_order_id']) && $_SESSION['payment_order_id'] === $order_id) {
            return $_SESSION['payment_booking_data'] ?? null;
        }
        return $_SESSION['payment_booking_data'] ?? null;
    }
    
    /**
     * Clear payment session
     */
    public function clearPaymentSession() {
        unset($_SESSION['payment_order_id']);
        unset($_SESSION['payment_booking_data']);
    }
    
    /**
     * Get payment status by order ID
     */
    public function getPaymentStatus($order_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.Status, p.Amount, p.PaymentMethod, p.PaymentDate, p.BookingId
            FROM Payment p 
            WHERE p.TransactionId = ?
            ORDER BY p.PaymentDate DESC 
            LIMIT 1
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Generate temporary email for guest users
     */
    private function generateTempEmail($name) {
        return strtolower(str_replace(' ', '', $name)) . '@demo.com';
    }
}
?>
