<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Bus.php');

$database = new Database();
$connection = $database->getConnection();
$busObj = new Bus($connection);

if (isset($_GET['bus_no'])) {
    $busObj->delete($_GET['bus_no']);
    header("Location: ../buslisting.php?delete_msg=Bus deleted successfully");
}
?>
