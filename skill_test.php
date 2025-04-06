<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

if (!isset($_GET['job_id'])) {
    echo "Job ID is missing!";
    exit();
}

$job_id = $_GET['job_id'];

// Get the skill_id for the job
$job_query = mysqli_query($conn, "SELECT skill_id FROM jobs WHERE job_id = $job_id");
if (!$job_query || mysqli_num_rows($job_query) === 0) {
    echo "Invalid Job ID!";
    exit();
}
$job_data = mysqli_fetch_assoc($job_query);
$skill_id = $job_data['skill_id'];

// Check if the user has already attempted the test for this job
$check_attempt = mysqli_query($conn, "SELECT * FROM test_attempts WHERE job_id = $job_id AND job_seeker_id = $jobseeker_id");
if (mysqli_num_rows($check_attempt) > 4) {
    echo "<h3 style='color:red;'>You have already attempted this skill test for this job.</h3>";
    exit();
}

// Fetch skill test questions
$questions_query = mysqli_query($conn, "SELECT * FROM questions WHERE skill_id = $skill_id");
$questions = [];
while ($row = mysqli_fetch_assoc($questions_query)) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Skill Test</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }
        .question-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .timer {
            background: #343a40;
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            font-size: 18px;
            margin-bottom: 20px;
            width: fit-content;
        }
        .submit-btn {
            padding: 10px 20px;
            background: #28a745;
            border: none;
            color: #fff;
            border-radius: 6px;
            font-size: 16px;
        }
    </style>
    <script>
        let minutes = 10;
        let seconds = 0;

        function updateTimer() {
            let timerElement = document.getElementById("timer");
            if (minutes === 0 && seconds === 0) {
                alert("Time's up! Submitting your test.");
                document.getElementById("test-form").submit();
                return;
            }

            if (seconds === 0) {
                minutes--;
                seconds = 59;
            } else {
                seconds--;
            }

            timerElement.textContent = `Time Left: ${minutes}m ${seconds < 10 ? '0' : ''}${seconds}s`;
        }

        setInterval(updateTimer, 1000);
    </script>
</head>
<body>
    <div class="timer" id="timer">Time Left: 10m 00s</div>

    <form id="test-form" action="submit_test.php" method="post">
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

        <?php
        if (count($questions) === 0) {
            echo "<p style='color:red;'>No questions found for this skill!</p>";
        } else {
            foreach ($questions as $index => $q) {
                echo "<div class='question-box'>";
                echo "<p><strong>Q" . ($index + 1) . ":</strong> " . htmlspecialchars($q['question_text']) . "</p>";
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($q["option$i"])) {
                        echo "<label><input type='radio' name='answers[" . $q['id'] . "]' value='$i'> " . htmlspecialchars($q["option$i"]) . "</label><br>";
                    }
                }
                echo "</div>";
            }

            echo "<button type='submit' class='submit-btn'>Submit Test</button>";
        }
        ?>
    </form>
</body>
</html>
