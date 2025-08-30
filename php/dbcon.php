<?php
$host = 'bosennoy016fmb5flv0m-mysql.services.clever-cloud.com';
$dbname = 'bosennoy016fmb5flv0m';
$username =  'ul9ivik7jhoj9kyh';
$password = 'iVbsGABNeLEWyG69bSqj';

try {
    $connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>
