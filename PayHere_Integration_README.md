# PayHere IPG Integration - Lanka Transit

This document explains the PayHere Internet Payment Gateway (IPG) integration implementation for the Lanka Transit bus booking system.

## Overview

The integration follows OOP principles and implements a secure payment flow:

1. **Seat Selection** → **Payment Gateway** → **Confirmation**
2. Payment verification using PayHere's security standards
3. Proper error handling and user feedback

## Files Created/Modified

### Core Payment Classes
- `/classes/Payment.php` - Main payment processing class (OOP)
- `/config/payhere_config.php` - Configuration file with placeholders

### Payment Flow Pages
- `/pages/payment.php` - Payment form and gateway redirect
- `/pages/payment_notify.php` - PayHere server callback handler
- `/pages/payment_return.php` - Success return handler
- `/pages/payment_cancel.php` - Cancel/failure handler
- `/pages/confirmation.php` - Updated to handle payment verification

### Modified Files
- `/pages/book.php` - Updated to redirect to payment instead of direct confirmation

## Configuration Setup

### 1. PayHere Credentials
Update `/config/payhere_config.php`:

```php
const MERCHANT_ID = 'YOUR_ACTUAL_MERCHANT_ID';
const MERCHANT_SECRET = 'YOUR_ACTUAL_MERCHANT_SECRET';
const SANDBOX_MODE = false; // Set to false for production
const BASE_URL = 'https://yourdomain.com/Lanka-Transit';
```

### 2. Database Requirements
Ensure your database has the required tables from `schema.sql`:
- `User`
- `Booking`
- `Payment`
- `Booking_2` (optional, for gender tracking)

## Payment Flow

### 1. User Journey
```
Seat Selection → Book Button → Payment Page → PayHere Gateway
                                     ↓
PayHere Processing → Return to Site → Confirmation Page
```

### 2. Technical Flow
```
book.php → payment.php → PayHere → payment_notify.php (background)
                              ↓
                         payment_return.php → confirmation.php
```

## Security Features

### 1. Hash Verification
- Payment requests use MD5 hash verification
- Notification responses are verified before processing
- Prevents tampering and ensures authenticity

### 2. Session Management
- Booking data stored securely in sessions
- Payment order IDs tracked across requests
- Session cleanup after completion

### 3. Database Transactions
- All booking/payment operations use database transactions
- Rollback on any failure ensures data consistency

## Error Handling

### 1. Payment Failures
- User redirected to payment_cancel.php
- Retry options provided
- Support contact information displayed

### 2. Technical Errors
- Comprehensive error logging
- Graceful failure with user-friendly messages
- Fallback to homepage with clear instructions

## Testing

### 1. Sandbox Mode
- Set `SANDBOX_MODE = true` in config
- Use PayHere sandbox credentials
- Test with sandbox payment methods

### 2. Test Cases
- Successful payment flow
- Payment cancellation
- Payment failure scenarios
- Network timeout handling

## Production Deployment

### 1. Required Updates
```php
// In payhere_config.php
const MERCHANT_ID = 'YOUR_LIVE_MERCHANT_ID';
const MERCHANT_SECRET = 'YOUR_LIVE_MERCHANT_SECRET';
const SANDBOX_MODE = false;
const BASE_URL = 'https://yourlivesite.com/Lanka-Transit';
```

### 2. Server Requirements
- PHP 7.4+
- PDO MySQL extension
- SSL certificate (required for notify_url)
- Public domain (localhost won't work for notifications)

### 3. Security Checklist
- [ ] Merchant secret kept secure
- [ ] HTTPS enabled for all payment pages
- [ ] Error reporting disabled in production
- [ ] Database credentials secured
- [ ] Session security configured

## API Endpoints

### 1. Payment Notification (payment_notify.php)
- **Method**: POST
- **Purpose**: Receives PayHere payment status
- **Authentication**: MD5 signature verification
- **Response**: HTTP 200 "OK" or 400 "FAILED"

### 2. Return URLs
- **Success**: payment_return.php
- **Cancel**: payment_cancel.php
- **Notify**: payment_notify.php (server callback)

## Troubleshooting

### 1. Common Issues
- **"Payment not confirmed"**: Check notify_url accessibility
- **"Hash mismatch"**: Verify merchant secret in config
- **"Booking not found"**: Check session handling

### 2. Debug Mode
Enable error logging in payment_notify.php for debugging:
```php
error_log("PayHere Notification: " . json_encode($_POST));
```

### 3. Logs to Check
- PHP error logs
- PayHere merchant portal logs
- Database transaction logs

## Support Contacts

- **PayHere Support**: support@payhere.lk
- **Technical Documentation**: https://www.payhere.lk/downloads/
- **Developer API**: https://support.payhere.lk/

## Class Structure (OOP Implementation)

```
Payment Class
├── generatePaymentForm()     // Creates PayHere form data
├── verifyPayment()          // Validates PayHere response
├── processPaymentNotification() // Handles server callback
├── getPaymentStatus()       // Retrieves payment info
├── storePaymentSession()    // Session management
└── clearPaymentSession()    // Cleanup

PayHereConfig Class
├── MERCHANT_ID             // Configuration constants
├── MERCHANT_SECRET
├── getCheckoutUrl()        // Dynamic URL generation
├── getReturnUrl()
├── getCancelUrl()
└── getNotifyUrl()
```

This implementation ensures secure, reliable payment processing following PayHere's best practices and OOP design principles.
