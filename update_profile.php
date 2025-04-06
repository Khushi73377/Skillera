<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$skills = $_POST['skills']; // comma-separated string

// File Upload: Resume
$resume_path = null;
if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    $resume_tmp = $_FILES['resume']['tmp_name'];
    $resume_name = basename($_FILES['resume']['name']);
    $resume_ext = pathinfo($resume_name, PATHINFO_EXTENSION);

    if (strtolower($resume_ext) === 'pdf') {
        $resume_path = "uploads/resumes/" . time() . "_" . $resume_name;
        move_uploaded_file($resume_tmp, $resume_path);
    }
}

// Update profile in database
$sql = "UPDATE job_seekers SET name=?, email=?, phone=?, address=?, skills=?" . ($resume_path ? ", resume=?" : "") . " WHERE id=?";
$stmt = $conn->prepare($sql);

if ($resume_path) {
    $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $skills, $resume_path, $jobseeker_id);
} else {
    $stmt->bind_param("sssssi", $name, $email, $phone, $address, $skills, $jobseeker_id);
}

if ($stmt->execute()) {
    header("Location: view_profile.php?success=1");
    exit();
} else {
    echo "❌ Error updating profile: " . $stmt->error;
}
?>
