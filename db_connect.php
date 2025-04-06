<?php
$servername = "localhost"; // Change if using a different server
$username = "root"; // Default for XAMPP
$password = ""; // Default for XAMPP
$dbname = "project"; // Make sure this is set!

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
