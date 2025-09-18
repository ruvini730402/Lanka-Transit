<?php
/**
 * PayHere Configuration
 * Uses environment variables for secure credential management
 * 
 * Configuration Priority:
 * 1. Environment Variables (Production)
 * 2. .env file (Development)
 * 3. Local config files (Local development)
 */

require_once __DIR__ . '/env_loader.php';

class PayHereConfig {
    // PayHere URLs
    const SANDBOX_URL = 'https://sandbox.payhere.lk/pay/checkout';
    const LIVE_URL = 'https://www.payhere.lk/pay/checkout';
    
    // Currency
    const CURRENCY = 'LKR';
    const COUNTRY = 'LK';
    
    /**
     * Get PayHere Merchant ID
     * @return string
     */
    public static function getMerchantId() {
        $merchantId = EnvLoader::get('PAYHERE_MERCHANT_ID');
        if (empty($merchantId)) {
            throw new Exception('PAYHERE_MERCHANT_ID environment variable is required');
        }
        return $merchantId;
    }
    
    /**
     * Get PayHere Merchant Secret
     * @return string
     */
    public static function getMerchantSecret() {
        $merchantSecret = EnvLoader::get('PAYHERE_MERCHANT_SECRET');
        if (empty($merchantSecret)) {
            throw new Exception('PAYHERE_MERCHANT_SECRET environment variable is required');
        }
        return $merchantSecret;
    }
    
    /**
     * Check if sandbox mode is enabled
     * @return bool
     */
    public static function isSandbox() {
        $sandbox = EnvLoader::get('PAYHERE_SANDBOX', 'true');
        return filter_var($sandbox, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Get base URL for the application
     * @return string
     */
    public static function getBaseUrl() {
        $baseUrl = EnvLoader::get('BASE_URL');
        if (empty($baseUrl)) {
            throw new Exception('BASE_URL environment variable is required');
        }
        return $baseUrl;
    }
    
    /**
     * Get currency code
     * @return string
     */
    public static function getCurrency() {
        return self::CURRENCY;
    }
    
    /**
     * Get country code
     * @return string
     */
    public static function getCountry() {
        return self::COUNTRY;
    }
    
    /**
     * Get PayHere checkout URL
     * @return string
     */
    public static function getCheckoutUrl() {
        return self::isSandbox() ? self::SANDBOX_URL : self::LIVE_URL;
    }
    
    /**
     * Get payment return URL
     * @return string
     */
    public static function getReturnUrl() {
        return self::getBaseUrl() . '/pages/payment_return.php';
    }
    
    /**
     * Get payment cancel URL
     * @return string
     */
    public static function getCancelUrl() {
        return self::getBaseUrl() . '/pages/payment_cancel.php';
    }
    
    /**
     * Get payment notification URL
     * @return string
     */
    public static function getNotifyUrl() {
        return self::getBaseUrl() . '/pages/payment_notify.php';
    }
    
    /**
     * Generate payment hash
     * @param array $data Payment data
     * @return string
     */
    public static function generateHash($data) {
        $hash = strtoupper(
            md5(
                self::getMerchantId() . 
                $data['order_id'] . 
                number_format($data['amount'], 2, '.', '') . 
                self::getCurrency() . 
                strtoupper(md5(self::getMerchantSecret()))
            )
        );
        return $hash;
    }
}
?>
