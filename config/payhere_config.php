<?php
/**
 * PayHere Configuration
 * Replace placeholders with actual PayHere credentials
 */

class PayHereConfig {
    // TODO: Replace with your actual PayHere credentials
    const MERCHANT_ID = 'YOUR_MERCHANT_ID_HERE';
    const MERCHANT_SECRET = 'YOUR_MERCHANT_SECRET_HERE';
    
    // Set to false for production
    const SANDBOX_MODE = true;
    
    // PayHere URLs
    const SANDBOX_URL = 'https://sandbox.payhere.lk/pay/checkout';
    const LIVE_URL = 'https://www.payhere.lk/pay/checkout';
    
    // Currency
    const CURRENCY = 'LKR';
    
    // Return URLs (Update with your domain)
    const BASE_URL = 'http://localhost/Lanka-Transit'; // TODO: Update with your actual domain
    
    public static function getCheckoutUrl() {
        return self::SANDBOX_MODE ? self::SANDBOX_URL : self::LIVE_URL;
    }
    
    public static function getReturnUrl() {
        return self::BASE_URL . '/pages/payment_return.php';
    }
    
    public static function getCancelUrl() {
        return self::BASE_URL . '/pages/payment_cancel.php';
    }
    
    public static function getNotifyUrl() {
        return self::BASE_URL . '/pages/payment_notify.php';
    }
}
?>
