<?php
// filepath: c:\xampp\htdocs\login page\connection.php   

$servername = "localhost";
$username = "root";
$password = ""; // Default password is empty
$dbname = "parkingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>