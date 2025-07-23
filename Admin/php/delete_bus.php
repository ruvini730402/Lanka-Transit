<?php
include('../dbcon.php');
include('Bus.php');

$busObj = new Bus($connection);

if (isset($_GET['bus_no'])) {
    $busObj->delete($_GET['bus_no']);
    header("Location: ../buslisting.php?delete_msg=Bus deleted successfully");
}
?>
