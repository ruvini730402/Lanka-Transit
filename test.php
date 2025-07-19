<?php
/**
 * Test Page - Verify System Setup
 * 
 * This page helps verify that the system is set up correctly
 */

require_once 'bootstrap.php';

// Test database connection
function testDatabaseConnection() {
    try {
        $db = DatabaseConfig::getInstance()->getConnection();
        return ['status' => 'success', 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

// Test models
function testModels() {
    try {
        $route = new Route();
        $bus = new Bus();
        return ['status' => 'success', 'message' => 'Models loaded successfully'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

// Test search functionality
function testSearchController() {
    try {
        $controller = new SearchController();
        return ['status' => 'success', 'message' => 'Search controller working'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

$tests = [
    'Database Connection' => testDatabaseConnection(),
    'Models' => testModels(),
    'Search Controller' => testSearchController()
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanka Transit - System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Lanka Transit - System Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            This page verifies that all system components are working correctly.
                        </p>
                        
                        <?php foreach ($tests as $testName => $result): ?>
                            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                <div class="me-3">
                                    <?php if ($result['status'] === 'success'): ?>
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($testName) ?></h6>
                                    <p class="mb-0 <?= $result['status'] === 'success' ? 'text-success' : 'text-danger' ?>">
                                        <?= htmlspecialchars($result['message']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-1"></i>
                                    Go to Homepage
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="api/search.php?action=origins" class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-code me-1"></i>
                                    Test API
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>System Information:</h6>
                            <ul class="list-unstyled small text-muted">
                                <li><strong>PHP Version:</strong> <?= PHP_VERSION ?></li>
                                <li><strong>Server Software:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></li>
                                <li><strong>Document Root:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?></li>
                                <li><strong>Current Time:</strong> <?= date('Y-m-d H:i:s') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
