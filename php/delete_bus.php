<?php

include('../dbcon.php');
include('../../classes/Bus.php');


$busObj = new Bus($connection);


if (isset($_GET['id'])) {
    $busObj->deleteBus($_GET['id']);
    header("Location: ../buslisting.php?delete_msg=Bus deleted successfully");
    exit;
}
?>
