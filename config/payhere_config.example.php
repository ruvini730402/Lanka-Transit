<?php
/**
 * PayHere Configuration Template
 * This file is safe for GitHub. Copy to payhere_config.php and configure.
 * 
 * Configuration Priority:
 * 1. Environment Variables (Production)
 * 2. .env file (Development)
 * 3. Local config files (Local development)
 */
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
        return $_ENV['PAYHERE_MERCHANT_ID'] ?? self::getLocalConfig('merchant_id') ?? 'your-merchant-id';
    }
    
    /**
     * Get PayHere Merchant Secret
     * @return string
     */
    public static function getMerchantSecret() {
        return $_ENV['PAYHERE_MERCHANT_SECRET'] ?? self::getLocalConfig('merchant_secret') ?? 'your-merchant-secret';
    }
    
    /**
     * Check if sandbox mode is enabled
     * @return bool
     */
    public static function isSandbox() {
        $sandbox = $_ENV['PAYHERE_SANDBOX'] ?? self::getLocalConfig('sandbox') ?? 'true';
        return filter_var($sandbox, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Get base URL for the application
     * @return string
     */
    public static function getBaseUrl() {
        return $_ENV['BASE_URL'] ?? self::getLocalConfig('base_url') ?? 'http://localhost';
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
    
    /**
     * Fallback to local config file if exists (not in repo)
     * @param string $key
     * @return mixed|null
     */
    private static function getLocalConfig($key) {
        $localConfigFile = __DIR__ . '/local_payhere_config.php';
        if (file_exists($localConfigFile)) {
            $config = include $localConfigFile;
            return $config[$key] ?? null;
        }
        return null;
    }
}
?>