<?php
session_start();
include 'config.php'; // Adjust path based on your structure

// Ensure the user is logged in
if (!isset($_SESSION['job_seeker_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    $job_seeker_id = $_SESSION['job_seeker_id'];

    // Check if the application belongs to the logged-in user and is withdrawable
    $check_sql = "SELECT status FROM job_applications WHERE application_id = ? AND seeker_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $application_id, $job_seeker_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['status'] == 'Pending') {
        // Withdraw the application (delete or update the status)
        $delete_sql = "DELETE FROM job_applications WHERE application_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $application_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Application withdrawn successfully.";
        } else {
            $_SESSION['message'] = "Error withdrawing application.";
        }
    } else {
        $_SESSION['message'] = "You can only withdraw pending applications.";
    }

    header("Location: applied-jobs.php");
    exit();
}
?>
