<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";
$skill_id = null;
$skill_name = "";

// Get the skill ID from the URL
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $skill_id = mysqli_real_escape_string($conn, $_GET["id"]);

    // Fetch the skill data based on the ID
    $sql_select = "SELECT id, skill_name FROM skills WHERE id = '$skill_id'";
    $result_select = mysqli_query($conn, $sql_select);

    if (mysqli_num_rows($result_select) == 1) {
        $row = mysqli_fetch_assoc($result_select);
        $skill_name = htmlspecialchars($row['skill_name']);
    } else {
        $message = "<div style='color: red;'>Error: Skill not found.</div>";
        $skill_id = null; // Reset skill ID if not found
    }
} else {
    $message = "<div style='color: red;'>Error: Invalid skill ID.</div>";
}

// Handle form submission for updating the skill
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_skill"])) {
    if (isset($_POST["edit_id"]) && is_numeric($_POST["edit_id"]) && isset($_POST["edit_skill"]) && !empty($_POST["edit_skill"])) {
        $edit_id = mysqli_real_escape_string($conn, $_POST["edit_id"]);
        $edit_skill = mysqli_real_escape_string($conn, $_POST["edit_skill"]);

        // Check if the updated skill name already exists for another skill
        $check_sql = "SELECT id FROM skills WHERE skill_name = '$edit_skill' AND id != '$edit_id'";
        $check_result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div style='color: red;'>Error: Skill '$edit_skill' already exists.</div>";
        } else {
            $update_sql = "UPDATE skills SET skill_name = '$edit_skill' WHERE id = '$edit_id'";
            if (mysqli_query($conn, $update_sql)) {
                $message = "<div style='color: green;'>Skill updated successfully! <a href='skill_management.php'>Back to Skill Management</a></div>";
                // Refetch the updated skill name
                $sql_select = "SELECT id, skill_name FROM skills WHERE id = '$edit_id'";
                $result_select = mysqli_query($conn, $sql_select);
                if (mysqli_num_rows($result_select) == 1) {
                    $row = mysqli_fetch_assoc($result_select);
                    $skill_name = htmlspecialchars($row['skill_name']);
                }
            } else {
                $message = "<div style='color: red;'>Error updating skill: " . mysqli_error($conn) . "</div>";
            }
        }
    } else {
        $message = "<div style='color: red;'>Error: Invalid input. Please ensure the skill name is not empty.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Skill | Skillera Admin (India Edition)</title>
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

        .edit-skill-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .edit-skill-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .edit-skill-container label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .edit-skill-container input[type="text"] {
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

        .edit-skill-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #58a6ff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        .edit-skill-container button:hover {
            background-color: #3d8bf1;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            color: #58a6ff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
            <li><a href="all_users.php">All Users</a></li>
            <li><a href="all_jobs.php">All Jobs Posted</a></li>
            <li><a href="job_applications.php">Job Applications</a></li>
            <li><a href="post_questions.php">Post Questions</a></li>
            <li><a href="manage_homepage.php">Manage Homepage Content</a></li>
            <li><a href="manage_pages.php">Manage Static Pages</a></li>
            <li><a href="blog_management.php">Blog Management</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="skill_management.php" class="active">Skill Management</a></li>
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="location_management.php">Location Management</a></li>
            <li><a href="recruiter_management.php">Recruiter Management</a></li>
            <li><a href="jobseeker_management.php">Job Seeker Management</a></li>
            <li><a href="ban_management.php">Ban Management</a></li>
            <li><a href="analytics.php">Detailed Analytics</a></li>
            <li><a href="export_data.php">Export Data</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content">
        <h1>Edit Skill</h1>

        <?php echo $message; ?>

        <?php if ($skill_id !== null): ?>
            <div class="edit-skill-container">
                <h3>Edit Skill</h3>
                <form method="POST" action="">
                    <input type="hidden" name="edit_id" value="<?php echo $skill_id; ?>">
                    <label for="edit_skill">Skill Name:</label>
                    <input type="text" name="edit_skill" id="edit_skill" value="<?php echo $skill_name; ?>" required>
                    <button type="submit" name="update_skill">Update Skill</button>
                </form>
            </div>
        <?php endif; ?>

        <p><a href="skill_management.php" class="back-link">Back to Skill Management</a></p>

    </div>

</body>
</html>