<?php
/**
 * Shared Hosting Database Connection Test
 * Diagnoses database connection issues specific to shared hosting environments
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>";
echo "<html><head><title>Database Connection Test - Lanka Transit</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";
echo "</head><body>";

echo "<h1>🔍 Database Connection Diagnostic</h1>";
echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Test 1: Check if .env file exists and is readable
echo "<h2>📄 Step 1: Environment File Check</h2>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    if (is_readable($envFile)) {
        echo "<span class='success'>✅ .env file exists and is readable</span><br>";
        echo "<span class='info'>📍 File path: " . realpath($envFile) . "</span><br>";
        echo "<span class='info'>📊 File size: " . filesize($envFile) . " bytes</span><br>";
    } else {
        echo "<span class='error'>❌ .env file exists but is not readable</span><br>";
    }
} else {
    echo "<span class='error'>❌ .env file not found at: $envFile</span><br>";
}

// Test 2: Check environment loader
echo "<h2>⚙️ Step 2: Environment Loader Test</h2>";
$envLoaderFile = __DIR__ . '/config/env_loader.php';
if (file_exists($envLoaderFile)) {
    echo "<span class='success'>✅ env_loader.php found</span><br>";
    try {
        require_once $envLoaderFile;
        echo "<span class='success'>✅ env_loader.php loaded successfully</span><br>";
        
        // Test environment loading
        EnvLoader::load();
        echo "<span class='success'>✅ Environment variables loaded</span><br>";
    } catch (Exception $e) {
        echo "<span class='error'>❌ Error loading env_loader.php: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span class='error'>❌ env_loader.php not found</span><br>";
}

// Test 3: Check database credentials from environment
echo "<h2>🔑 Step 3: Database Credentials Check</h2>";
try {
    if (class_exists('EnvLoader')) {
        $dbHost = EnvLoader::get('DB_HOST');
        $dbName = EnvLoader::get('DB_NAME');
        $dbUsername = EnvLoader::get('DB_USERNAME');
        $dbPassword = EnvLoader::get('DB_PASSWORD');
        
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
        echo "<tr><td>DB_HOST</td><td>" . ($dbHost ? htmlspecialchars($dbHost) : '<em>not set</em>') . "</td><td>" . ($dbHost ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td></tr>";
        echo "<tr><td>DB_NAME</td><td>" . ($dbName ? htmlspecialchars($dbName) : '<em>not set</em>') . "</td><td>" . ($dbName ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td></tr>";
        echo "<tr><td>DB_USERNAME</td><td>" . ($dbUsername ? htmlspecialchars($dbUsername) : '<em>not set</em>') . "</td><td>" . ($dbUsername ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td></tr>";
        echo "<tr><td>DB_PASSWORD</td><td>" . ($dbPassword ? str_repeat('*', strlen($dbPassword)) : '<em>not set</em>') . "</td><td>" . ($dbPassword ? "<span class='success'>✅</span>" : "<span class='warning'>⚠️</span>") . "</td></tr>";
        echo "</table>";
    } else {
        echo "<span class='error'>❌ EnvLoader class not available</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error reading credentials: " . $e->getMessage() . "</span><br>";
}

// Test 4: Check PHP PDO MySQL support
echo "<h2>🐘 Step 4: PHP MySQL Support</h2>";
if (extension_loaded('pdo')) {
    echo "<span class='success'>✅ PDO extension loaded</span><br>";
    
    if (extension_loaded('pdo_mysql')) {
        echo "<span class='success'>✅ PDO MySQL driver loaded</span><br>";
        
        $drivers = PDO::getAvailableDrivers();
        echo "<span class='info'>📋 Available PDO drivers: " . implode(', ', $drivers) . "</span><br>";
    } else {
        echo "<span class='error'>❌ PDO MySQL driver not loaded</span><br>";
    }
} else {
    echo "<span class='error'>❌ PDO extension not loaded</span><br>";
}

// Test 5: Attempt database connection
echo "<h2>🔌 Step 5: Database Connection Test</h2>";
if (isset($dbHost, $dbName, $dbUsername) && extension_loaded('pdo_mysql')) {
    try {
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        echo "<span class='info'>🔗 Attempting connection to: $dsn</span><br>";
        
        $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]);
        
        echo "<span class='success'>✅ Database connection successful!</span><br>";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT VERSION() as version, NOW() as current_time");
        $result = $stmt->fetch();
        echo "<span class='success'>✅ Query test successful</span><br>";
        echo "<span class='info'>🗄️ MySQL Version: " . $result['version'] . "</span><br>";
        echo "<span class='info'>🕐 Server Time: " . $result['current_time'] . "</span><br>";
        
        // Check if Lanka Transit tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<span class='success'>✅ Found " . count($tables) . " tables in database</span><br>";
            echo "<span class='info'>📋 Tables: " . implode(', ', array_slice($tables, 0, 10)) . (count($tables) > 10 ? '...' : '') . "</span><br>";
        } else {
            echo "<span class='warning'>⚠️ No tables found in database - may need to import schema</span><br>";
        }
        
    } catch (PDOException $e) {
        echo "<span class='error'>❌ Database connection failed</span><br>";
        echo "<span class='error'>🚫 Error: " . $e->getMessage() . "</span><br>";
        echo "<span class='error'>🔢 Error Code: " . $e->getCode() . "</span><br>";
        
        // Common shared hosting database issues
        echo "<h3>💡 Common Shared Hosting Solutions:</h3>";
        echo "<ul>";
        echo "<li><strong>Wrong host:</strong> Try 'localhost' instead of external host</li>";
        echo "<li><strong>Database prefix:</strong> Your hosting may add a prefix to database name</li>";
        echo "<li><strong>Username prefix:</strong> Username might need hosting account prefix</li>";
        echo "<li><strong>Firewall:</strong> External databases may be blocked</li>";
        echo "<li><strong>Port:</strong> Default MySQL port (3306) might be different</li>";
        echo "</ul>";
    }
} else {
    echo "<span class='error'>❌ Missing required database credentials or MySQL support</span><br>";
}

// Test 6: Shared hosting specific checks
echo "<h2>🏠 Step 6: Shared Hosting Environment</h2>";

// Check for cPanel
if (isset($_ENV['HTTP_HOST']) && (strpos($_ENV['HTTP_HOST'], 'cpanel') !== false || 
    file_exists('/usr/local/cpanel') || file_exists($HOME . '/.cpanel'))) {
    echo "<span class='info'>🎛️ cPanel environment detected</span><br>";
}

// Check server info
echo "<span class='info'>🖥️ Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</span><br>";
echo "<span class='info'>🐘 PHP Version: " . PHP_VERSION . "</span><br>";

// Check common hosting paths
$commonPaths = ['/home', '/public_html', '/www', '/httpdocs'];
foreach ($commonPaths as $path) {
    if (is_dir($path)) {
        echo "<span class='info'>📁 Path exists: $path</span><br>";
    }
}

echo "<hr>";
echo "<h2>📋 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>If external database fails:</strong> Contact your hosting provider for local database credentials</li>";
echo "<li><strong>Check hosting control panel:</strong> Look for 'MySQL Databases' or 'Database' section</li>";
echo "<li><strong>Create local database:</strong> Most shared hosts provide local MySQL databases</li>";
echo "<li><strong>Update .env file:</strong> Use hosting-provided database credentials</li>";
echo "<li><strong>Import schema:</strong> Upload your database schema to the hosting database</li>";
echo "</ol>";

echo "<p><a href='/diagnostic.php'>← Back to System Diagnostic</a> | <a href='/'>Return to Home</a></p>";
echo "</body></html>";
?>