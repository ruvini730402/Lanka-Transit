<?php
session_start();
include('../dbcon.php');
include('Bus.php');

$busObj = new Bus($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_no = strtoupper(trim($_POST['bus_no']));
    $route = trim($_POST['route']);
    $contact = trim($_POST['driver_contact']);
    $status = trim($_POST['status']);
    $seats = trim($_POST['seat_count']);

    // Validate bus number format
    if (!preg_match('/^[A-Z]{2,3}-\d{4}$/', $bus_no)) {
        $_SESSION['bus_error'] = "Invalid Bus Number format. Use NB-1234.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }
    // Validate contact number
    if (!preg_match('/^\d{10}$/', $contact)) {
        $_SESSION['bus_error'] = "Invalid Driver Contact number. Must be 10 digits.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }
    // Validate seat count
    if (!is_numeric($seats) || $seats <= 0) {
        $_SESSION['bus_error'] = "Invalid Seat Count.";
        $_SESSION['bus_form'] = $_POST;
        header("Location: ../buslisting.php");
        exit();
    }

    if ($busObj->insert($bus_no, $route, $contact, $status, $seats)) {
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
