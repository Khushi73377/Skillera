<?php
session_start();
include("db_connection.php");

if (!isset($_SESSION['jobseeker_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

if (!isset($_GET['job_id'])) {
    echo "<script>alert('Invalid Job ID'); window.location.href='dashboard.php';</script>";
    exit();
}

$job_id = intval($_GET['job_id']);

// Get skill_id from the job
$result = mysqli_query($conn, "SELECT skill_id FROM jobs WHERE id = $job_id");
$row = mysqli_fetch_assoc($result);
$skill_id = $row['skill_id'];

// Check if already attempted
$check = mysqli_query($conn, "SELECT * FROM test_results WHERE jobseeker_id = $jobseeker_id AND skill_id = $skill_id");
if (mysqli_num_rows($check) > 0) {
    echo "<script>alert('You have already attempted this skill test.'); window.location.href='dashboard.php';</script>";
    exit();
}

// Get questions for the skill
$questions = mysqli_query($conn, "SELECT * FROM questions WHERE skill_id = $skill_id ORDER BY RAND() LIMIT 10");
?>


<!DOCTYPE html>
<html>
<head>
    <title>Skill Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef;
            margin: 20px;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            width: 80%;
            margin: auto;
            box-shadow: 0 0 10px #aaa;
        }
        .question {
            margin-bottom: 20px;
        }
        .timer {
            float: right;
            font-size: 18px;
            color: red;
            font-weight: bold;
        }
        .submit-btn {
            padding: 10px 20px;
            background: green;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: darkgreen;
        }
    </style>
    <script>
        // 10-minute countdown
        let minutes = 10;
        let seconds = 0;

        function updateTimer() {
            let timerDisplay = document.getElementById("timer");
            if (minutes === 0 && seconds === 0) {
                alert("Time's up! Submitting your answers.");
                document.getElementById("testForm").submit();
            } else {
                if (seconds === 0) {
                    minutes--;
                    seconds = 59;
                } else {
                    seconds--;
                }
                timerDisplay.innerText = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            }
        }

        setInterval(updateTimer, 1000);
    </script>
</head>
<body>
    <div class="container">
        <h2>Skill Test</h2>
        <div class="timer" id="timer">Time Left: 10:00</div>
        <form method="POST" action="submit-test.php" id="testForm">
    <input type="hidden" name="skill_id" value="<?php echo $skill_id; ?>">
    <?php
    $qno = 1;
    while ($row = mysqli_fetch_assoc($questions)) {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<strong>Q$qno. {$row['question_text']}</strong><br>";
        echo "<input type='radio' name='answers[{$row['id']}]' value='1'> {$row['option1']}<br>";
        echo "<input type='radio' name='answers[{$row['id']}]' value='2'> {$row['option2']}<br>";
        if (!empty($row['option3'])) {
            echo "<input type='radio' name='answers[{$row['id']}]' value='3'> {$row['option3']}<br>";
        }
        if (!empty($row['option4'])) {
            echo "<input type='radio' name='answers[{$row['id']}]' value='4'> {$row['option4']}<br>";
        }
        echo "</div>";
        $qno++;
    }
    ?>
    <button type="submit" class="submit-button">Submit Test</button>
</form>

    </div>
</body>
</html>
