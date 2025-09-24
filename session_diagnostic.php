<?php
/**
 * Session Diagnostic Tool for Production Environment
 */
session_start();

echo "<h2>Session Diagnostic Information</h2>\n";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>\n";

// Session Configuration
echo "<h3>Session Configuration</h3>\n";
echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Setting</th><th>Value</th></tr>\n";
echo "<tr><td>session.save_path</td><td>" . ini_get('session.save_path') . "</td></tr>\n";
echo "<tr><td>session.name</td><td>" . ini_get('session.name') . "</td></tr>\n";
echo "<tr><td>session.cookie_lifetime</td><td>" . ini_get('session.cookie_lifetime') . "</td></tr>\n";
echo "<tr><td>session.cookie_path</td><td>" . ini_get('session.cookie_path') . "</td></tr>\n";
echo "<tr><td>session.cookie_domain</td><td>" . ini_get('session.cookie_domain') . "</td></tr>\n";
echo "<tr><td>session.cookie_secure</td><td>" . (ini_get('session.cookie_secure') ? 'Yes' : 'No') . "</td></tr>\n";
echo "<tr><td>session.cookie_httponly</td><td>" . (ini_get('session.cookie_httponly') ? 'Yes' : 'No') . "</td></tr>\n";
echo "<tr><td>session.gc_maxlifetime</td><td>" . ini_get('session.gc_maxlifetime') . " seconds</td></tr>\n";
echo "</table>\n";

// Session Status
echo "<h3>Current Session Status</h3>\n";
echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Property</th><th>Value</th></tr>\n";
echo "<tr><td>Session ID</td><td>" . session_id() . "</td></tr>\n";
echo "<tr><td>Session Status</td><td>" . session_status() . " (" . 
    (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 
     (session_status() === PHP_SESSION_DISABLED ? 'Disabled' : 'None')) . ")</td></tr>\n";
echo "<tr><td>Session Data Count</td><td>" . count($_SESSION) . "</td></tr>\n";
echo "</table>\n";

// Session Data
echo "<h3>Session Data</h3>\n";
if (!empty($_SESSION)) {
    echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>\n";
} else {
    echo "<p style='color: red;'>No session data found.</p>\n";
}

// Test Session Write
echo "<h3>Session Write Test</h3>\n";
$_SESSION['test_key'] = 'test_value_' . time();
echo "<p>✅ Test data written to session: " . $_SESSION['test_key'] . "</p>\n";

// Server Environment
echo "<h3>Server Environment</h3>\n";
echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Variable</th><th>Value</th></tr>\n";
echo "<tr><td>SERVER_NAME</td><td>" . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</td></tr>\n";
echo "<tr><td>HTTP_HOST</td><td>" . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</td></tr>\n";
echo "<tr><td>HTTPS</td><td>" . ($_SERVER['HTTPS'] ?? 'Not set') . "</td></tr>\n";
echo "<tr><td>DOCUMENT_ROOT</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</td></tr>\n";
echo "<tr><td>PHP_VERSION</td><td>" . PHP_VERSION . "</td></tr>\n";
echo "</table>\n";

// Directory Permissions
echo "<h3>Directory Permissions</h3>\n";
$session_path = session_save_path();
if (empty($session_path)) {
    $session_path = sys_get_temp_dir();
}

echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Directory</th><th>Exists</th><th>Writable</th><th>Permissions</th></tr>\n";

$dirs = [
    'Session Save Path' => $session_path,
    'Current Directory' => __DIR__,
    'Temp Directory' => sys_get_temp_dir()
];

foreach ($dirs as $name => $path) {
    $exists = is_dir($path) ? 'Yes' : 'No';
    $writable = is_writable($path) ? 'Yes' : 'No';
    $perms = is_dir($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    echo "<tr><td>$name</td><td>$exists</td><td>$writable</td><td>$perms</td></tr>\n";
}
echo "</table>\n";

// Cookies
echo "<h3>Cookies</h3>\n";
if (!empty($_COOKIE)) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
    echo "<tr><th>Cookie Name</th><th>Value</th></tr>\n";
    foreach ($_COOKIE as $name => $value) {
        echo "<tr><td>" . htmlspecialchars($name) . "</td><td>" . htmlspecialchars(substr($value, 0, 50)) . 
             (strlen($value) > 50 ? '...' : '') . "</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<p>No cookies found.</p>\n";
}

// Recommendations
echo "<h3>Recommendations for Production</h3>\n";
echo "<ul>\n";

if (empty(ini_get('session.save_path'))) {
    echo "<li style='color: orange;'>⚠️ Session save path is empty. Consider setting a specific path.</li>\n";
}

if (!ini_get('session.cookie_httponly')) {
    echo "<li style='color: orange;'>⚠️ session.cookie_httponly is disabled. Enable for security.</li>\n";
}

if ($_SERVER['HTTPS'] ?? false && !ini_get('session.cookie_secure')) {
    echo "<li style='color: orange;'>⚠️ HTTPS detected but session.cookie_secure is disabled.</li>\n";
}

if (ini_get('session.gc_maxlifetime') < 1800) {
    echo "<li style='color: orange;'>⚠️ Session garbage collection lifetime is less than 30 minutes.</li>\n";
}

echo "<li style='color: green;'>✅ Use session_regenerate_id() for security.</li>\n";
echo "<li style='color: green;'>✅ Store critical data in database, not just sessions.</li>\n";
echo "<li style='color: green;'>✅ Implement session validation and recovery.</li>\n";
echo "</ul>\n";

echo "<hr>\n";
echo "<p><strong>Session diagnostic completed.</strong></p>\n";
?>