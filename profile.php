<?php
session_start();
include("db.php");

if (!isset($_SESSION['recruiter_id'])) {
    header("Location: login-recruiter.html");
    exit();
}

$recruiter_id = $_SESSION['recruiter_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $company = $_POST['company'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($password) {
        $query = "UPDATE recruiters SET name=?, email=?, company=?, password=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $name, $email, $company, $password, $recruiter_id);
    } else {
        $query = "UPDATE recruiters SET name=?, email=?, company=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $name, $email, $company, $recruiter_id);
    }

    if ($stmt->execute()) {
        $_SESSION['profile_message'] = "Profile updated successfully!";
        $_SESSION['profile_message_type'] = "success"; // Add message type
    } else {
        $_SESSION['profile_message'] = "Error updating profile.";
        $_SESSION['profile_message_type'] = "error"; // Add message type
    }

    $stmt->close();
    $conn->close();

    header("Location: update-profile.php"); // Redirect to update-profile.php
    exit();
}
?>
