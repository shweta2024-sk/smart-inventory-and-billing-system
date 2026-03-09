<?php
session_start();
include("../config/db.php");

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = $username;
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Invalid Username or Password";
    }
} else {
    header("Location: ../index.php");
}
?>