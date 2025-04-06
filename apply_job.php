<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cover_letter = $conn->real_escape_string($_POST['cover_letter']);

    // Check if already applied
    $check_query = "SELECT * FROM job_applications WHERE job_id = ? AND seeker_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $job_id, $jobseeker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "⚠️ You have already applied for this job.";
    } else {
        // Insert application
        $insert_query = "INSERT INTO job_applications (job_id, seeker_id, cover_letter, status, applied_at, skill_test_score) VALUES (?, ?, ?, 'Applied', NOW(), NULL)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iis", $job_id, $jobseeker_id, $cover_letter);

        if ($stmt->execute()) {
            $message = "✅ Your application has been submitted successfully!";
        } else {
            $message = "❌ Error submitting application.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Job</title>
    <style>
        body {
            font-family: Arial;
            background: #eef2f5;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
            resize: vertical;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .msg {
            font-size: 16px;
            margin-top: 15px;
            color: green;
        }
        .error {
            color: red;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply for Job</h2>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST") { ?>
            <p class="msg <?= strpos($message, '❌') !== false || strpos($message, '⚠️') !== false ? 'error' : '' ?>">
                <?= $message ?>
            </p>
            <a href="search_jobs.php" class="back-link">🔙 Back to Job Listings</a>
        <?php } else { ?>
            <form method="post">
                <label for="cover_letter"><strong>Cover Letter:</strong></label><br>
                <textarea name="cover_letter" required placeholder="Write a brief message to the employer..."></textarea>
                <button type="submit" class="btn">Submit Application</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
