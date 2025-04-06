<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

if (!isset($_GET['job_id'])) {
    header("Location: search_jobs.php"); // Redirect if job ID is missing
    exit();
}

$job_id = $_GET['job_id'];
$jobseeker_id = $_SESSION['jobseeker_id'];

// --- Check for previous attempts ---
$check_attempt_query = "SELECT COUNT(*) FROM test_attempts WHERE jobseeker_id = ? AND job_id = ?";
$stmt_check = $conn->prepare($check_attempt_query);
$stmt_check->bind_param("ii", $jobseeker_id, $job_id);
$stmt_check->execute();
$stmt_check->bind_result($attempt_count);
$stmt_check->fetch();
$stmt_check->close();

if ($attempt_count > 0) {
    $error_message = "You have already attempted the skill test for this job.";
} else {
    // --- Fetch 8 random questions (replace with your actual logic) ---
    $query_questions = "SELECT * FROM test_questions ORDER BY RAND() LIMIT 8";
    $result_questions = $conn->query($query_questions);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Test</title>
    <link rel="stylesheet" href="js_style.css">
    <style>
        /* Specific styles for the dashboard layout (copied from jobseeker_dashboard.php) */
        body {
            display: flex;
            background-color: #f0f2f5; /* Light grey background */
        }

        .sidebar {
            background-color: #343a40; /* Dark sidebar background */
            color: #fff;
            width: 220px; /* Slightly narrower sidebar */
            padding-top: 20px;
            height: 100vh; /* Make sidebar full height */
            position: fixed; /* Stick to the side */
            left: 0;
            top: 0;
            overflow-y: auto; /* Allow scrolling if content overflows */
        }

        .sidebar-title {
            padding: 20px;
            text-align: left;
            margin-bottom: 20px;
            color: #fff;
            font-size: 1.5em;
            font-weight: bold;
        }

        .sidebar-menu {
            padding: 0 20px;
        }

        .sidebar-menu h3 {
            color: #adb5bd; /* Light grey heading for sections */
            padding: 10px 0;
            margin-bottom: 5px;
            font-size: 1em;
            font-weight: normal;
            text-transform: uppercase;
        }

        .sidebar-menu a {
            display: block;
            padding: 10px 0; /* Less vertical padding */
            text-decoration: none;
            color: #ddd;
            transition: background-color 0.3s ease;
            text-align: left;
            border-left: 3px solid transparent; /* For active indicator */
            margin-bottom: 5px;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: #28a745; /* Example active color */
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 220px; /* Adjust margin to accommodate sidebar width */
        }

        .content-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h2 {
            margin-right: 20px;
            margin-bottom: 0;
            color: #28a745; /* Example primary color */
            font-size: 1.8em;
        }

        .content-header p {
            margin-bottom: 0;
            color: #6c757d;
        }

        /* --- Specific styles for skill test page --- */
        .test-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .timer {
            font-size: 1.5em;
            font-weight: bold;
            color: #dc3545; /* Red color for timer */
            margin-bottom: 15px;
            text-align: center;
        }

        .question {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .question h4 {
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .options label {
            display: block;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .submit-test-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 20px;
        }

        .submit-test-btn:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-title">Job Seeker</div>
        <div class="sidebar-menu">
            <h3>Main</h3>
            <a href="jobseeker_dashboard.php">Dashboard</a>
            <a href="search_jobs.php" class="active">Search Jobs</a>
            <a href="JS_profile.php">View Profile</a>
            <a href="applied_jobs.php">Applied Jobs</a>
            <h3>Account</h3>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="content">
        <div class="content-header">
            <h2>Skill Test</h2>
            <p>Complete the test within the time limit.</p>
        </div>

        <div class="test-container">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php elseif (isset($result_questions) && $result_questions->num_rows > 0): ?>
                <div class="timer" id="timer">10:00</div>
                <form id="skillTestForm" action="process_skill_test.php" method="post">
                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                    <?php $question_number = 1; ?>
                    <?php while ($row_question = $result_questions->fetch_assoc()): ?>
                        <div class="question">
                            <h4>Question <?php echo $question_number; ?>: <?php echo htmlspecialchars($row_question['question_text']); ?></h4>
                            <div class="options">
                                <label><input type="radio" name="answer[<?php echo $row_question['question_id']; ?>]" value="A"> A) <?php echo htmlspecialchars($row_question['option_a']); ?></label><br>
                                <label><input type="radio" name="answer[<?php echo $row_question['question_id']; ?>]" value="B"> B) <?php echo htmlspecialchars($row_question['option_b']); ?></label><br>
                                <label><input type="radio" name="answer[<?php echo $row_question['question_id']; ?>]" value="C"> C) <?php echo htmlspecialchars($row_question['option_c']); ?></label><br>
                                <label><input type="radio" name="answer[<?php echo $row_question['question_id']; ?>]" value="D"> D) <?php echo htmlspecialchars($row_question['option_d']); ?></label><br>
                            </div>
                        </div>
                        <?php $question_number++; ?>
                    <?php endwhile; ?>
                    <button type="submit" class="submit-test-btn">Submit Test</button>
                </form>
            <?php else: ?>
                <p class="no-questions">No skill test questions available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let testTimer;
        const testDuration = 10 * 60; // 10 minutes in seconds
        let timeLeft = testDuration;
        const timerDisplay = document.getElementById('timer');
        const skillTestForm = document.getElementById('skillTestForm');

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function startTestTimer() {
            updateTimerDisplay();
            testTimer = setInterval(function() {
                timeLeft--;
                updateTimerDisplay();
                if (timeLeft < 0) {
                    clearInterval(testTimer);
                    alert("Time's up! Your test will be submitted automatically.");
                    if (skillTestForm) {
                        skillTestForm.submit();
                    }
                }
            }, 1000);
        }

        window.onload = function() {
            startTestTimer();
        };
    </script>

</body>
</html>