<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch existing skills
$sql_skills = "SELECT id, skill_name FROM skills ORDER BY skill_name ASC";
$result_skills = mysqli_query($conn, $sql_skills);
$skills = mysqli_fetch_all($result_skills, MYSQLI_ASSOC);

// Handle form submission for adding/editing/deleting skills
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        if ($_POST["action"] == "add") {
            $new_skill = mysqli_real_escape_string($conn, $_POST["new_skill"]);
            if (!empty($new_skill)) {
                // Check if skill already exists
                $check_sql = "SELECT id FROM skills WHERE skill_name = '$new_skill'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $message = "<div style='color: red;'>Error: Skill '$new_skill' already exists.</div>";
                } else {
                    $insert_sql = "INSERT INTO skills (skill_name) VALUES ('$new_skill')";
                    if (mysqli_query($conn, $insert_sql)) {
                        $message = "<div style='color: green;'>Skill '$new_skill' added successfully!</div>";
                        // Refetch skills
                        $result_skills = mysqli_query($conn, $sql_skills);
                        $skills = mysqli_fetch_all($result_skills, MYSQLI_ASSOC);
                    } else {
                        $message = "<div style='color: red;'>Error adding skill: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                $message = "<div style='color: red;'>Error: Skill name cannot be empty.</div>";
            }
        } elseif ($_POST["action"] == "edit") {
            $edit_id = mysqli_real_escape_string($conn, $_POST["edit_id"]);
            $edit_skill = mysqli_real_escape_string($conn, $_POST["edit_skill"]);
            if (!empty($edit_skill)) {
                // Check if the updated skill name already exists for another skill
                $check_sql = "SELECT id FROM skills WHERE skill_name = '$edit_skill' AND id != '$edit_id'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $message = "<div style='color: red;'>Error: Skill '$edit_skill' already exists.</div>";
                } else {
                    $update_sql = "UPDATE skills SET skill_name = '$edit_skill' WHERE id = '$edit_id'";
                    if (mysqli_query($conn, $update_sql)) {
                        $message = "<div style='color: green;'>Skill updated successfully!</div>";
                        // Refetch skills
                        $result_skills = mysqli_query($conn, $sql_skills);
                        $skills = mysqli_fetch_all($result_skills, MYSQLI_ASSOC);
                    } else {
                        $message = "<div style='color: red;'>Error updating skill: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                $message = "<div style='color: red;'>Error: Skill name cannot be empty.</div>";
            }
        } elseif ($_POST["action"] == "delete") {
            $delete_id = mysqli_real_escape_string($conn, $_POST["delete_id"]);
            $delete_sql = "DELETE FROM skills WHERE id = '$delete_id'";
            if (mysqli_query($conn, $delete_sql)) {
                $message = "<div style='color: green;'>Skill deleted successfully!</div>";
                // Refetch skills
                $result_skills = mysqli_query($conn, $sql_skills);
                $skills = mysqli_fetch_all($result_skills, MYSQLI_ASSOC);
            } else {
                $message = "<div style='color: red;'>Error deleting skill: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Management | Skillera Admin (India Edition)</title>
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

        .add-skill-container, .skills-list-container, .edit-skill-form {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .add-skill-container h3, .skills-list-container h3, .edit-skill-form h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .add-skill-container label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .add-skill-container input[type="text"] {
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

        .add-skill-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #238636;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        .add-skill-container button:hover {
            background-color: #2ea043;
        }

        .skills-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .skills-list-table th, .skills-list-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .skills-list-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .skills-list-table .actions a, .skills-list-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .skills-list-table .actions a {
            text-decoration: none;
            color: #58a6ff;
            font-size: 14px;
        }

        .skills-list-table .actions a:hover {
            text-decoration: underline;
        }

        .skills-list-table .actions form button {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }

        .skills-list-table .actions form button:hover {
            text-decoration: underline;
        }

        .edit-skill-form label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .edit-skill-form input[type="text"] {
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

        .edit-skill-form button {
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

        .edit-skill-form button:hover {
            background-color: #3d8bf1;
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
        <h1>Skill Management</h1>

        <?php echo $message; ?>

        <div class="add-skill-container">
            <h3>Add New Skill</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <label for="new_skill">Skill Name:</label>
                <input type="text" name="new_skill" id="new_skill" required>
                <button type="submit">Add Skill</button>
            </form>
        </div>

        <div class="skills-list-container">
            <h3>Existing Skills</h3>
            <?php if (empty($skills)): ?>
                <p>No skills have been added yet.</p>
            <?php else: ?>
                <table class="skills-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Skill Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td><?php echo $skill['id']; ?></td>
                                <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                                <td class="actions">
                                    <a href="edit_skill.php?id=<?php echo $skill['id']; ?>">Edit</a>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="delete_id" value="<?php echo $skill['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this skill?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>