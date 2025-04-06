<?php
$servername = "localhost";  // Your XAMPP server
$username = "root";         // Default XAMPP MySQL username
$password = "";             // Default is empty in XAMPP
$database = "project"; // Change this to your actual database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>