<?php
session_start();
include("db.php");

// ✅ Ensure recruiter is logged in
if (!isset($_SESSION['recruiter_id'])) {
    header("Location: login-recruiter.html");
    exit();
}

// ✅ Ensure job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid job ID.");
}

$job_id = (int)$_GET['id'];
$recruiter_id = $_SESSION['recruiter_id'];

// ✅ Check if the job belongs to the logged-in recruiter
$stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = ? AND recruiter_id = ?");
$stmt->bind_param("ii", $job_id, $recruiter_id);

if ($stmt->execute()) {
    echo "<script>alert('Job deleted successfully!'); window.location='manage-jobs.php';</script>";
} else {
    echo "<script>alert('Error deleting job.'); window.location='manage-jobs.php';</script>";
}

$stmt->close();
$conn->close();
?>
