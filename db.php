<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "billing_system";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
