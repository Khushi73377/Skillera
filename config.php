<?php
$host = "localhost";
$user = "root"; // Change if needed
$password = ""; // Change if you have a password
$database = "project"; // Change to your actual database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
