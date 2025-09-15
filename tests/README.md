# Lanka Transit - Test Suite

This folder contains test scripts for validating the payment return page functionality.

## Test Files

### 📋 `test_payment_suite.php`
**Main Test Controller**
- Unified interface for running all payment return tests
- Bootstrap UI with professional design
- Provides access to both success and failure tests
- Includes testing instructions and cleanup functionality

**Access**: `http://localhost/Lanka-Transit/tests/test_payment_suite.php`

### ✅ `test_payment_success.php`
**Payment Success Scenario Tests**
- Creates mock successful payment data
- Inserts test payment record in database
- Sets up proper session data
- Tests payment details display and auto-redirect

**Features Tested**:
- Payment details card display
- Amount formatting with currency
- Status badge styling
- Auto-redirect after 5 seconds
- Database integration
- Session data handling

### ❌ `test_payment_failure.php`
**Payment Failure Scenario Tests**
- Multiple failure scenarios:
  1. No Order ID provided
  2. Order not found in database
  3. Payment still pending
  4. Payment explicitly failed

**Features Tested**:
- Error message handling
- Loading state display
- Auto-refresh behavior
- Navigation fallbacks
- Order ID validation

### 🧹 `cleanup_test_data.php`
**Test Data Cleanup Script**
- Removes test payment records from database
- JSON API endpoint for cleanup operations
- Safety: Only removes records with test prefixes
- Returns cleanup results count

## How to Run Tests

### Method 1: Use the Test Suite (Recommended)
1. Open `test_payment_suite.php` in your browser
2. Click "Run Success Tests" or "Run Failure Tests"
3. Follow the on-screen instructions

### Method 2: Direct Access
- Success tests: `tests/test_payment_success.php`
- Failure tests: `tests/test_payment_failure.php`

## Test Data Prefixes

Test payment records use these OrderID prefixes:
- `LT-TEST-*` - Success scenario tests
- `LT-PENDING-*` - Pending payment tests
- `LT-FAILED-*` - Failed payment tests
- `LT-NOTFOUND-*` - Order not found tests

## Database Requirements

Tests require:
- `Payment` table with columns: `OrderID`, `Amount`, `Currency`, `Status`, `PaymentMethod`, `PaymentDate`
- `Database` class in `../classes/Database.php`
- `Payment` class in `../classes/Payment.php`

## Cleanup

After testing, use the cleanup functionality to remove test records:
- Via test suite interface (recommended)
- Direct call to `cleanup_test_data.php`

## Testing Checklist

### Success Tests ✅
- [ ] Payment details display correctly
- [ ] Amount shows with proper currency formatting
- [ ] Status badge appears green with "Success"
- [ ] Auto-redirect works after 5 seconds
- [ ] Order ID is properly formatted

### Failure Tests ❌
- [ ] No Order ID scenario shows appropriate error
- [ ] Order not found shows processing state
- [ ] Pending payment shows loading spinner
- [ ] Failed payment shows error state
- [ ] Auto-refresh works every 5 seconds
- [ ] Navigation buttons (Home, Refresh) work

### UI/UX Tests 🎨
- [ ] Responsive design on mobile/tablet
- [ ] Loading animations work properly
- [ ] Error messages are user-friendly
- [ ] Icons and styling match Lanka Transit theme
- [ ] Auto-redirect timing is appropriate

---

**Note**: These tests simulate real payment scenarios without actually processing payments through PayHere. They test the payment return page logic and UI components only.
