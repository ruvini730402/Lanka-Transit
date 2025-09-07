<?php
session_start();
include('../dbcon.php');
include('Bus.php');

$busObj = new Bus($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_no = strtoupper(trim($_POST['bus_no']));
    $route_id = trim($_POST['route']);
    $admin_id = trim($_POST['driver_contact']);
    $capacity = trim($_POST['seat_count']);

    // Validate bus number format
    if (!preg_match('/^[A-Z]{2,3}-\d{4}$/', $bus_no)) {
        $_SESSION['bus_error'] = "Invalid Bus Number format. Use NB-1234.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }
    // Validate route ID
    if (!is_numeric($route_id) || $route_id <= 0) {
        $_SESSION['bus_error'] = "Invalid Route ID. Must be a positive number.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }
    // Validate admin ID
    if (!is_numeric($admin_id) || $admin_id <= 0) {
        $_SESSION['bus_error'] = "Invalid Admin ID. Must be a positive number.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }
    // Validate capacity
    if (!is_numeric($capacity) || $capacity <= 0) {
        $_SESSION['bus_error'] = "Invalid Capacity.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }

    if ($busObj->insert($bus_no, $route_id, $admin_id, '', $capacity)) {
        $_SESSION['bus_success'] = "Bus added successfully.";
        header("Location: ../buslisting.php");
        exit();
    } else {
        $_SESSION['bus_error'] = "Insert failed. Possible duplicate bus number or database error.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }
}
?>
