<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch all skills for the dropdown
$sql_skills = "SELECT id AS skill_id, skill_name FROM skills";
$result_skills = mysqli_query($conn, $sql_skills);
$skills = mysqli_fetch_all($result_skills, MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST["skill_id"]) && !empty($_POST["skill_id"]) &&
        isset($_POST["question_text"]) && !empty($_POST["question_text"]) &&
        isset($_POST["option1"]) && !empty($_POST["option1"]) &&
        isset($_POST["option2"]) && !empty($_POST["option2"]) &&
        isset($_POST["option3"]) && isset($_POST["option4"]) && // option4 is nullable
        isset($_POST["correct_option"]) && !empty($_POST["correct_option"])
    ) {
        $skill_id = mysqli_real_escape_string($conn, $_POST["skill_id"]);
        $question_text = mysqli_real_escape_string($conn, $_POST["question_text"]);
        $option1 = mysqli_real_escape_string($conn, $_POST["option1"]);
        $option2 = mysqli_real_escape_string($conn, $_POST["option2"]);
        $option3 = mysqli_real_escape_string($conn, $_POST["option3"]);
        $option4 = !empty($_POST["option4"]) ? mysqli_real_escape_string($conn, $_POST["option4"]) : NULL;
        $correct_option = mysqli_real_escape_string($conn, $_POST["correct_option"]);

        $sql_insert = "INSERT INTO questions (skill_id, question_text, option1, option2, option3, option4, correct_option, created_at)
                       VALUES ('$skill_id', '$question_text', '$option1', '$option2', '$option3', " . ($option4 === NULL ? 'NULL' : "'$option4'") . ", '$correct_option', NOW())";

        if (mysqli_query($conn, $sql_insert)) {
            $message = "<div style='color: green;'>Question posted successfully!</div>";
        } else {
            $message = "<div style='color: red;'>Error posting question: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div style='color: red;'>Please fill in all required fields (Skill, Question, Option 1, Option 2, Option 3, Correct Option).</div>";
    }
}

// Fetch existing questions to display (optional, can be modified to filter by skill)
$sql_select_questions = "SELECT q.id, s.skill_name, q.question_text, q.option1, q.option2, q.option3, q.option4, q.correct_option, q.created_at
                         FROM questions q
                         INNER JOIN skills s ON q.skill_id = s.id
                         ORDER BY q.created_at DESC";
$result_questions = mysqli_query($conn, $sql_select_questions);
$existing_questions = mysqli_fetch_all($result_questions, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Questions | Skillera Admin (India Edition)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0d1117;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #161b22;
            color: #c9d1d9;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            color: #58a6ff;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            color: #c9d1d9;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
        }

        .sidebar ul li a:hover {
            background-color: #30363d;
            color: #58a6ff;
        }

        .content {
            flex-grow: 1;
            padding: 30px;
            box-sizing: border-box;
        }

        .content h1 {
            color: #58a6ff;
            margin-bottom: 20px;
        }

        .post-question-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .post-question-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .post-question-container form label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .post-question-container form select,
        .post-question-container form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #30363d;
            border-radius: 6px;
            background-color: #0d1117;
            color: #c9d1d9;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }

        .post-question-container form button {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #238636;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .post-question-container form button:hover {
            background-color: #2ea043;
        }

        .existing-questions-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
        }

        .existing-questions-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .existing-questions-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .existing-questions-list li {
            padding: 10px 0;
            border-bottom: 1px solid #30363d;
        }

        .existing-questions-list li:last-child {
            border-bottom: none;
        }

        .logout-btn {
            display: block;
            margin-top: 20px;
            padding: 10px 15px;
            border: 1px solid #30363d;
            border-radius: 6px;
            background-color: transparent;
            color: #c9d1d9;
            font-size: 16px;
            cursor: pointer;
            transition: border-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
            width: 150px;
            text-align: center;
        }

        .logout-btn:hover {
            border-color: #58a6ff;
            color: #58a6ff;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="all_users.php" class="active">All Users</a></li>
            <li><a href="all_jobs.php">All Jobs Posted</a></li>
            <li><a href="job_applications.php">Job Applications</a></li>
            <li><a href="skill_management.php">Skill Management</a></li>
            <li><a href="recruiter_management.php">Recruiter Management</a></li>
            <li><a href="jobseeker_management.php">Job Seeker Management</a></li>
            <li><a href="analytics.php">Detailed Analytics</a></li>
            <li><a href="export_data.php">Export Data</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content">
        <h1>Post Questions</h1>

        <div class="post-question-container">
            <h3>Post a New Question</h3>
            <form method="POST" action="">
                <label for="skill_id">Skill:</label>
                <select name="skill_id" id="skill_id" required>
                    <option value="">Select a Skill</option>
                    <?php foreach ($skills as $skill): ?>
                        <option value="<?php echo $skill['skill_id']; ?>"><?php echo htmlspecialchars($skill['skill_name']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="question_text">Question Text:</label>
                <input type="text" name="question_text" id="question_text" required>

                <label for="option1">Option 1:</label>
                <input type="text" name="option1" id="option1" required>

                <label for="option2">Option 2:</label>
                <input type="text" name="option2" id="option2" required>

                <label for="option3">Option 3:</label>
                <input type="text" name="option3" id="option3" required>

                <label for="option4">Option 4 (Optional):</label>
                <input type="text" name="option4" id="option4">

                <label for="correct_option">Correct Option (Enter 1, 2, 3, or 4):</label>
                <input type="number" name="correct_option" id="correct_option" min="1" max="4" required>

                <button type="submit">Post Question</button>
            </form>
            <?php echo $message; ?>
        </div>

        <div class="existing-questions-container">
            <h3>Existing Questions</h3>
            <?php if (empty($existing_questions)): ?>
                <p>No questions have been posted yet.</p>
            <?php else: ?>
                <ul class="existing-questions-list">
                    <?php foreach ($existing_questions as $question): ?>
                        <li>
                            <strong>Skill:</strong> <?php echo htmlspecialchars($question['skill_name']); ?><br>
                            <strong>Question:</strong> <?php echo htmlspecialchars($question['question_text']); ?><br>
                            <strong>Options:</strong> 1) <?php echo htmlspecialchars($question['option1']); ?>, 2) <?php echo htmlspecialchars($question['option2']); ?>, 3) <?php echo htmlspecialchars($question['option3']); ?><?php if (!empty($question['option4'])): ?>, 4) <?php echo htmlspecialchars($question['option4']); ?><?php endif; ?><br>
                            <strong>Correct Option:</strong> <?php echo $question['correct_option']; ?>
                            (Posted on: <?php echo $question['created_at']; ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>