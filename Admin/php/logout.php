<?php
require_once __DIR__ . '/../../includes/session_config.php';
session_unset();   // Remove all session variables
session_destroy(); // Destroy the session
header("Location: ../../auth/Logout.php");
exit();
