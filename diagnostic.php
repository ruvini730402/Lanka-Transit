<?php
// Production Environment Diagnostic Script
// This script helps identify issues in the production environment

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Lanka Transit - Production Diagnostic</h1>";
echo "<hr>";

// 1. PHP Environment Check
echo "<h2>1. PHP Environment</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>Current Directory: " . getcwd() . "</li>";
echo "<li>Script Path: " . $_SERVER['SCRIPT_FILENAME'] . "</li>";
echo "</ul>";

// 2. File System Check
echo "<h2>2. File System Check</h2>";
$requiredFiles = [
    'index.php',
    'classes/Database.php',
    'classes/Announcement.php',
    'config/env_loader.php',
    'config/database_config.php',
    'includes/header.php',
    'includes/footer.php',
    '.env'
];

echo "<ul>";
foreach ($requiredFiles as $file) {
    $exists = file_exists($file);
    $readable = $exists ? is_readable($file) : false;
    
    echo "<li>$file: ";
    if ($exists) {
        echo "<span style='color: green;'>EXISTS</span>";
        if ($readable) {
            echo " - <span style='color: green;'>READABLE</span>";
        } else {
            echo " - <span style='color: red;'>NOT READABLE</span>";
        }
    } else {
        echo "<span style='color: red;'>MISSING</span>";
    }
    echo "</li>";
}
echo "</ul>";

// 3. Directory Structure
echo "<h2>3. Directory Structure</h2>";
$directories = ['classes', 'config', 'includes', 'pages', 'auth', 'Admin', 'assets', 'PHPMailer'];
echo "<ul>";
foreach ($directories as $dir) {
    $exists = is_dir($dir);
    echo "<li>$dir/: ";
    if ($exists) {
        echo "<span style='color: green;'>EXISTS</span>";
        $files = scandir($dir);
        $fileCount = count($files) - 2; // Exclude . and ..
        echo " ($fileCount files)";
    } else {
        echo "<span style='color: red;'>MISSING</span>";
    }
    echo "</li>";
}
echo "</ul>";

// 4. Environment Variables
echo "<h2>4. Environment Check</h2>";
if (file_exists('.env')) {
    echo "<p><span style='color: green;'>.env file exists</span></p>";
    
    // Try to load environment
    try {
        require_once 'config/env_loader.php';
        EnvLoader::load();
        echo "<p><span style='color: green;'>Environment loader loaded successfully</span></p>";
        
        // Check key environment variables
        $envVars = ['DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD'];
        echo "<ul>";
        foreach ($envVars as $var) {
            $value = EnvLoader::get($var);
            echo "<li>$var: ";
            if (!empty($value)) {
                echo "<span style='color: green;'>SET</span> (length: " . strlen($value) . ")";
            } else {
                echo "<span style='color: red;'>NOT SET</span>";
            }
            echo "</li>";
        }
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p><span style='color: red;'>Error loading environment: " . $e->getMessage() . "</span></p>";
    }
} else {
    echo "<p><span style='color: red;'>.env file missing</span></p>";
}

// 5. Database Connection Test
echo "<h2>5. Database Connection Test</h2>";
try {
    require_once 'classes/Database.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "<p><span style='color: green;'>Database connection successful</span></p>";
        
        // Test a simple query
        try {
            $stmt = $conn->query("SELECT 1 as test");
            $result = $stmt->fetch();
            if ($result && $result['test'] == 1) {
                echo "<p><span style='color: green;'>Database query test successful</span></p>";
            }
        } catch (Exception $e) {
            echo "<p><span style='color: orange;'>Database connected but query failed: " . $e->getMessage() . "</span></p>";
        }
        
    } else {
        echo "<p><span style='color: red;'>Database connection failed</span></p>";
    }
} catch (Exception $e) {
    echo "<p><span style='color: red;'>Database connection error: " . $e->getMessage() . "</span></p>";
}

// 6. Class Loading Test
echo "<h2>6. Class Loading Test</h2>";
$classes = ['Database', 'Announcement'];
foreach ($classes as $className) {
    try {
        require_once "classes/$className.php";
        echo "<p><span style='color: green;'>$className class loaded successfully</span></p>";
        
        // Try to instantiate
        if ($className === 'Database') {
            $instance = new Database();
        } elseif ($className === 'Announcement') {
            $instance = new Announcement();
        }
        echo "<p><span style='color: green;'>$className instance created successfully</span></p>";
        
    } catch (Exception $e) {
        echo "<p><span style='color: red;'>$className error: " . $e->getMessage() . "</span></p>";
    }
}

// 7. Permissions Check
echo "<h2>7. Permissions Check</h2>";
echo "<ul>";
echo "<li>Current user: " . get_current_user() . "</li>";
echo "<li>Web directory writable: " . (is_writable('.') ? 'YES' : 'NO') . "</li>";
echo "<li>Config directory readable: " . (is_readable('config') ? 'YES' : 'NO') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Diagnostic complete.</strong> If any items show as RED or ERROR, those need to be fixed for the application to work properly.</p>";
echo "<p>Generated at: " . date('Y-m-d H:i:s') . "</p>";
?>