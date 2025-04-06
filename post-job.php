<?php
session_start();
include("db.php"); // Ensure database connection

// Debugging: Check the session
echo "<pre>Session: ";
print_r($_SESSION);
echo "</pre>";

// ✅ Check session correctly
if (!isset($_SESSION['recruiter_id'])) { // Change 'user_id' to 'recruiter_id'
    die("You must be logged in to post a job.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);

    $recruiter_id = $_SESSION['recruiter_id']; // ✅ Fetch recruiter ID from session

    // Insert job details into the jobs table
    $insert_query = "INSERT INTO jobs (title, skills_required, location, salary_range, recruiter_id) 
                        VALUES ('$title', '$skills', '$location', '$salary', '$recruiter_id')";

    if (mysqli_query($conn, $insert_query)) {
        header("Location: manage-jobs.php?success=Job posted successfully!");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
}
?>