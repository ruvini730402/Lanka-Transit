<?php
/**
 * Deployment Status Checker
 * Verifies if cPanel deployment is working and shows deployment information
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>";
echo "<html><head><title>Deployment Status - Lanka Transit</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";
echo "</head><body>";

echo "<h1>🚀 Deployment Status Check</h1>";
echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Check 1: Deployment info file
echo "<h2>📄 Step 1: Deployment Information</h2>";
$deploymentInfoFile = __DIR__ . '/deployment.info';
if (file_exists($deploymentInfoFile)) {
    $deploymentInfo = file_get_contents($deploymentInfoFile);
    echo "<span class='success'>✅ Deployment info found</span><br>";
    echo "<pre>" . htmlspecialchars($deploymentInfo) . "</pre>";
} else {
    echo "<span class='error'>❌ deployment.info file not found - deployment may not have run</span><br>";
}

// Check 2: Current file timestamps
echo "<h2>🕒 Step 2: File Modification Times</h2>";
$importantFiles = [
    'index.php',
    'db_test.php',
    'diagnostic.php', 
    'debug_index.php',
    '.env',
    '.htaccess',
    'config/database_config.php'
];

echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>File</th><th>Exists</th><th>Last Modified</th><th>Age</th></tr>";

foreach ($importantFiles as $file) {
    $filepath = __DIR__ . '/' . $file;
    $exists = file_exists($filepath);
    
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td>" . ($exists ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td>";
    
    if ($exists) {
        $mtime = filemtime($filepath);
        $age = time() - $mtime;
        $ageText = '';
        
        if ($age < 60) {
            $ageText = $age . " seconds ago";
        } elseif ($age < 3600) {
            $ageText = round($age/60) . " minutes ago";
        } elseif ($age < 86400) {
            $ageText = round($age/3600) . " hours ago";
        } else {
            $ageText = round($age/86400) . " days ago";
        }
        
        echo "<td>" . date('Y-m-d H:i:s', $mtime) . "</td>";
        echo "<td>$ageText</td>";
    } else {
        echo "<td colspan='2'><em>File not found</em></td>";
    }
    echo "</tr>";
}
echo "</table>";

// Check 3: Environment variables
echo "<h2>⚙️ Step 3: Environment Check</h2>";
echo "<span class='info'>📍 Current working directory: " . getcwd() . "</span><br>";
echo "<span class='info'>🖥️ Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</span><br>";
echo "<span class='info'>🐘 PHP Version: " . PHP_VERSION . "</span><br>";

// Check if we're in the right directory structure
$expectedPath = '/public_html/transit.uvacell.com';
$currentPath = $_SERVER['DOCUMENT_ROOT'] ?? getcwd();
if (strpos($currentPath, $expectedPath) !== false) {
    echo "<span class='success'>✅ Appears to be in correct deployment directory</span><br>";
} else {
    echo "<span class='warning'>⚠️ May not be in expected deployment directory</span><br>";
    echo "<span class='info'>📁 Document root: $currentPath</span><br>";
}

// Check 4: Git information (if available)
echo "<h2>📦 Step 4: Version Information</h2>";
$gitHead = __DIR__ . '/.git/HEAD';
if (file_exists($gitHead)) {
    $gitInfo = file_get_contents($gitHead);
    echo "<span class='info'>🔀 Git HEAD: " . htmlspecialchars(trim($gitInfo)) . "</span><br>";
} else {
    echo "<span class='info'>📝 No .git directory found (normal for deployment)</span><br>";
}

// Check for version file or commit info
$possibleVersionFiles = ['VERSION', 'version.txt', '.version'];
foreach ($possibleVersionFiles as $versionFile) {
    if (file_exists(__DIR__ . '/' . $versionFile)) {
        $version = file_get_contents(__DIR__ . '/' . $versionFile);
        echo "<span class='info'>🏷️ Version: " . htmlspecialchars(trim($version)) . "</span><br>";
        break;
    }
}

// Check 5: Diagnostic files availability
echo "<h2>🔧 Step 5: Diagnostic Tools Status</h2>";
$diagnosticFiles = [
    'db_test.php' => 'Database Connection Test',
    'diagnostic.php' => 'System Diagnostic', 
    'debug_index.php' => 'Debug Version of Main App'
];

foreach ($diagnosticFiles as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<span class='success'>✅ $description available</span> - <a href='/$file'>Test $file</a><br>";
    } else {
        echo "<span class='error'>❌ $description missing</span><br>";
    }
}

// Check 6: cPanel specific indicators
echo "<h2>🎛️ Step 6: cPanel Environment Detection</h2>";
$cpanelIndicators = [
    '/usr/local/cpanel' => 'cPanel installation directory',
    '/home' => 'Home directory structure',
    '.cpanel' => 'cPanel configuration'
];

$cpanelDetected = false;
foreach ($cpanelIndicators as $path => $description) {
    if (file_exists($path) || is_dir($path)) {
        echo "<span class='success'>✅ $description found</span><br>";
        $cpanelDetected = true;
    }
}

if (!$cpanelDetected) {
    echo "<span class='info'>ℹ️ cPanel indicators not detected (may be normal)</span><br>";
}

// Manual deployment instructions
echo "<hr>";
echo "<h2>🔧 Manual Deployment Instructions</h2>";
echo "<div style='background:#e8f4fd;padding:15px;border-radius:5px;'>";
echo "<h3>If automatic deployment isn't working:</h3>";
echo "<ol>";
echo "<li><strong>Check cPanel Git™ Version Control:</strong>";
echo "<ul><li>Login to your cPanel</li><li>Go to 'Git™ Version Control'</li><li>Verify your repository is connected</li><li>Click 'Update' or 'Pull' to manually deploy</li></ul></li>";
echo "<li><strong>Alternative - Manual File Upload:</strong>";
echo "<ul><li>Download your repository as ZIP</li><li>Upload via cPanel File Manager</li><li>Extract to public_html/transit.uvacell.com/</li></ul></li>";
echo "<li><strong>Check deployment path:</strong>";
echo "<ul><li>Verify files are in: <code>/home/anagatha/public_html/transit.uvacell.com/</code></li></ul></li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<h2>🔗 Quick Links</h2>";
echo "<p>";
echo "<a href='/db_test.php'>🔍 Database Test</a> | ";
echo "<a href='/diagnostic.php'>🛠️ System Diagnostic</a> | ";
echo "<a href='/debug_index.php'>🐛 Debug Main App</a> | ";
echo "<a href='/'>🏠 Main Application</a>";
echo "</p>";

echo "</body></html>";
?>