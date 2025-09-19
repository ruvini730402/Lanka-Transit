<?php
// Version information for Lanka Transit
// This file is updated on each deployment to track versions

return [
    'version' => '1.0.0',
    'build_date' => '2025-09-19',
    'commit_hash' => 'latest',
    'environment' => 'production',
    'deployment_method' => 'cpanel-git',
    'last_updated' => date('Y-m-d H:i:s'),
    'files_included' => [
        'diagnostic_tools' => ['db_test.php', 'diagnostic.php', 'debug_index.php', 'deployment_status.php'],
        'error_pages' => ['404.html', '500.html'],
        'config_files' => ['.htaccess', '.env', 'config/'],
        'main_app' => ['index.php', 'pages/', 'classes/', 'assets/']
    ]
];
?>