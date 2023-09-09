<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$email = $_SESSION['email']; 
$phone = $_SESSION['phone']; 

echo $username . "<br>";
echo $email . "<br>";
echo $phone . "<br>";
?>

