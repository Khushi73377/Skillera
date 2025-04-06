<?php
// Database Connection
$servername = "localhost"; // XAMPP default server
$username = "root"; // Default XAMPP MySQL user
$password = ""; // No password by default
$database = "skill_era"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    // Check if email exists
    $query_email = "SELECT id FROM job_seekers WHERE email = '$email'";
    $result_email = mysqli_query($conn, $query_email);

    // Check if contact exists
    $query_contact = "SELECT id FROM job_seekers WHERE contact = '$contact'";
    $result_contact = mysqli_query($conn, $query_contact);

    if (mysqli_num_rows($result_email) > 0) {
        echo "email_exists";
    } elseif (mysqli_num_rows($result_contact) > 0) {
        echo "contact_exists";
    } else {
        echo "ok";
    }
}

// Close connection
$conn->close();
?>
