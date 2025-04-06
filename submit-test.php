<?php
session_start();
include("db_connection.php");

$jobseeker_id = $_SESSION['jobseeker_id'];
$skill_id = intval($_POST['skill_id']);
$answers = $_POST['answers'];

// Check if test already attempted
$check = mysqli_query($conn, "SELECT * FROM test_results WHERE jobseeker_id = $jobseeker_id AND skill_id = $skill_id");
if (mysqli_num_rows($check) > 0) {
    echo "<script>alert('You have already attempted this skill test.'); window.location.href='dashboard.php';</script>";
    exit();
}

// Fetch correct answers
$question_ids = implode(',', array_keys($answers));
$query = "SELECT id, correct_option FROM questions WHERE id IN ($question_ids)";
$result = mysqli_query($conn, $query);

$correct = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $qid = $row['id'];
    $correct_option = $row['correct_option'];
    if (isset($answers[$qid]) && $answers[$qid] == $correct_option) {
        $correct++;
    }
}

// Insert test result
$score = $correct;
$attempt_time = date("Y-m-d H:i:s");
mysqli_query($conn, "INSERT INTO test_results (jobseeker_id, skill_id, score, attempt_time) 
    VALUES ($jobseeker_id, $skill_id, $score, '$attempt_time')");

// Send result to recruiter (for applied jobs)
$jobResult = mysqli_query($conn, "SELECT DISTINCT job_id FROM job_applications WHERE jobseeker_id = $jobseeker_id");
while ($job = mysqli_fetch_assoc($jobResult)) {
    $job_id = $job['job_id'];

    // Check if this job requires the same skill
    $skillMatch = mysqli_query($conn, "SELECT recruiter_id FROM jobs WHERE id = $job_id AND skill_id = $skill_id");
    if ($row = mysqli_fetch_assoc($skillMatch)) {
        $recruiter_id = $row['recruiter_id'];
        // Optional: log or notify recruiter (e.g., insert to `notifications` or update a field)
        // echo "Notified recruiter ID: $recruiter_id for job $job_id";
    }
}

// Allow only if passed
if ($score >= 8) {
    echo "<script>alert('Congratulations! You passed the skill test and can proceed to apply.'); window.location.href='apply-job.php?skill_id=$skill_id';</script>";
} else {
    echo "<script>alert('You did not pass the skill test. You cannot apply for this job.'); window.location.href='dashboard.php';</script>";
}
?>
