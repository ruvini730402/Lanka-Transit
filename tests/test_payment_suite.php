<?php
/**
 * Test Suite Controller for Payment Return Page Testing
 * This provides a unified interface for testing both success and failure scenarios
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Return Page Test Suite - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .test-card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .test-card:hover {
            border-color: #800000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .success-card { border-color: #198754; background: #f8fff9; }
        .failure-card { border-color: #dc3545; background: #fff8f8; }
        .btn-test {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            border: none;
            color: white;
        }
        .btn-test:hover {
            background: linear-gradient(135deg, #800000 0%, #4B0000 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold" style="color: #800000;">
                        <i class="fas fa-vial me-3"></i>Payment Return Page Test Suite
                    </h1>
                    <p class="lead">Comprehensive testing for payment success and failure scenarios</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Payment Success Tests -->
            <div class="col-lg-6">
                <div class="test-card success-card h-100 p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                        <h3 class="mt-3 text-success">Payment Success Tests</h3>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Test Coverage:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Successful payment processing</li>
                            <li><i class="fas fa-check text-success me-2"></i>Payment details display</li>
                            <li><i class="fas fa-check text-success me-2"></i>Auto-redirect functionality</li>
                            <li><i class="fas fa-check text-success me-2"></i>Database integration</li>
                            <li><i class="fas fa-check text-success me-2"></i>Session management</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Expected Results:</h6>
                        <div class="alert alert-success">
                            <small>
                                ✓ Green success card with payment details<br>
                                ✓ Order ID, amount, status, method display<br>
                                ✓ Auto-redirect after 5 seconds<br>
                                ✓ Professional success messaging
                            </small>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="test_payment_success.php" class="btn btn-success btn-lg" target="_blank">
                            <i class="fas fa-play me-2"></i>Run Success Tests
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payment Failure Tests -->
            <div class="col-lg-6">
                <div class="test-card failure-card h-100 p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                        <h3 class="mt-3 text-danger">Payment Failure Tests</h3>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Test Scenarios:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-times text-danger me-2"></i>No Order ID provided</li>
                            <li><i class="fas fa-times text-danger me-2"></i>Order not found in database</li>
                            <li><i class="fas fa-clock text-warning me-2"></i>Payment still pending</li>
                            <li><i class="fas fa-times text-danger me-2"></i>Payment explicitly failed</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Expected Results:</h6>
                        <div class="alert alert-warning">
                            <small>
                                ⚠ Processing/error messages<br>
                                ⏳ Loading spinner animations<br>
                                🔄 Auto-refresh every 5 seconds<br>
                                🏠 Navigation options (Home, Refresh)
                            </small>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="test_payment_failure.php" class="btn btn-warning btn-lg" target="_blank">
                            <i class="fas fa-bug me-2"></i>Run Failure Tests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Instructions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Testing Instructions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>For Success Tests:</h5>
                                <ol>
                                    <li>Click "Run Success Tests" above</li>
                                    <li>Review the test setup information</li>
                                    <li>Click the green "Test Payment Success Page" button</li>
                                    <li>Verify the payment details are displayed correctly</li>
                                    <li>Confirm auto-redirect works (wait 5 seconds)</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h5>For Failure Tests:</h5>
                                <ol>
                                    <li>Click "Run Failure Tests" above</li>
                                    <li>Select different failure scenarios</li>
                                    <li>Click the yellow "Test Payment Failure Page" button</li>
                                    <li>Verify appropriate error/processing messages</li>
                                    <li>Test the auto-refresh functionality</li>
                                </ol>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-lightbulb me-2"></i>Testing Tips:</h6>
                            <ul class="mb-0">
                                <li>Open tests in new tabs to compare different scenarios</li>
                                <li>Check browser developer tools for console errors</li>
                                <li>Verify database records are created for success tests</li>
                                <li>Test on different devices/screen sizes for responsiveness</li>
                                <li>Clear browser cache between tests if needed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Cleanup -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-broom me-2"></i>Test Data Cleanup</h5>
                    </div>
                    <div class="card-body">
                        <p>After testing, you may want to clean up test payment records from the database:</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-warning" onclick="cleanupTestData()">
                                <i class="fas fa-trash me-2"></i>Clean Test Records
                            </button>
                            <small class="text-muted align-self-center">
                                Removes payment records with OrderID starting with 'LT-TEST-', 'LT-PENDING-', 'LT-FAILED-', 'LT-NOTFOUND-'
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cleanupTestData() {
            if (confirm('Are you sure you want to remove all test payment records? This action cannot be undone.')) {
                fetch('cleanup_test_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Successfully removed ${data.count} test records.`);
                    } else {
                        alert('Error cleaning up test data: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
    </script>
</body>
</html>
