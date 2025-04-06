<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $skill_id = $_POST['skill_id'];
    $answers = $_POST['answers'] ?? [];
    $jobseeker_id = $_SESSION['jobseeker_id'];

    // Fetch all correct answers for the skill
    $correct_answers_query = "SELECT question_id, correct_answer FROM questions WHERE skill_id = ?";
    $stmt_correct = $conn->prepare($correct_answers_query);
    $stmt_correct->bind_param("i", $skill_id);
    $stmt_correct->execute();
    $correct_answers_result = $stmt_correct->get_result();
    $correct_answers = $correct_answers_result->fetch_all(MYSQLI_ASSOC);
    $stmt_correct->close();

    $score = 0;
    foreach ($correct_answers as $correct_answer) {
        $question_id = $correct_answer['question_id'];
        $correct = $correct_answer['correct_answer'];
        if (isset($answers[$question_id]) && $answers[$question_id] == $correct) {
            $score++;
        }
    }

    $passed = $score >= 8;

    // Store the test attempt
    $insert_attempt_query = "INSERT INTO test_attempts (job_seeker_id, job_id, skill_id, score, attempted_at, passed)
                             VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt_insert = $conn->prepare($insert_attempt_query);
    $stmt_insert->bind_param("iiiis", $jobseeker_id, $job_id, $skill_id, $score, $passed ? 1 : 0);
    $stmt_insert->execute();
    $stmt_insert->close();

    if ($passed) {
        header("Location: apply_job.php?job_id=" . $job_id . "&test_passed=1&score=" . $score);
        exit();
    } else {
        // Check attempt count for this job
        $check_attempts_query = "SELECT COUNT(*) AS attempts FROM test_attempts
                                 WHERE job_seeker_id = ? AND job_id = ?";
        $stmt_check = $conn->prepare($check_attempts_query);
        $stmt_check->bind_param("ii", $jobseeker_id, $job_id);
        $stmt_check->execute();
        $attempts_result = $stmt_check->get_result()->fetch_assoc();
        $attempts = $attempts_result['attempts'];
        $stmt_check->close();

        if ($attempts >= 1) { // Only one attempt allowed
            // Ban the job seeker for this specific job and skill
            $disqualify_query = "INSERT INTO job_application_disqualifications (job_seeker_id, job_id, skill_id, disqualified_at)
                                 VALUES (?, ?, ?, NOW())";
            $stmt_disqualify = $conn->prepare($disqualify_query);
            $stmt_disqualify