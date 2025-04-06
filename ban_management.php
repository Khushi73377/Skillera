<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch all banned users (you might need to adjust this query based on how you implement bans)
$sql_banned_users = "SELECT b.id, b.user_id, b.user_type, b.ban_reason, b.ban_date,
                           CASE
                               WHEN b.user_type = 'jobseeker' THEN js.name
                               WHEN b.user_type = 'recruiter' THEN r.name
                               ELSE 'Unknown'
                           END AS user_name,
                           CASE
                               WHEN b.user_type = 'recruiter' THEN r.company
                               ELSE ''
                           END AS company_name,
                           b.banned_by_admin_id
                    FROM bans b
                    LEFT JOIN job_seekers js ON b.user_id = js.id AND b.user_type = 'jobseeker'
                    LEFT JOIN recruiters r ON b.user_id = r.id AND b.user_type = 'recruiter'
                    ORDER BY b.ban_date DESC";
$result_banned_users = mysqli_query($conn, $sql_banned_users);
$banned_users = mysqli_fetch_all($result_banned_users, MYSQLI_ASSOC);

// Handle unbanning action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "unban") {
    $ban_id = mysqli_real_escape_string($conn, $_POST["ban_id"]);

    $unban_sql = "DELETE FROM bans WHERE id = '$ban_id'";
    if (mysqli_query($conn, $unban_sql)) {
        $message = "<div style='color: green;'>User unbanned successfully!</div>";
        // Refetch banned users
        $result_banned_users = mysqli_query($conn, $sql_banned_users);
        $banned_users = mysqli_fetch_all($result_banned_users, MYSQLI_ASSOC);
    } else {
        $message = "<div style='color: red;'>Error unbanning user: " . mysqli_error($conn) . "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ban Management | Skillera Admin (India Edition)</title>
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

        .banned-users-list-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .banned-users-list-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .banned-users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .banned-users-table th, .banned-users-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .banned-users-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .banned-users-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .banned-users-table .actions button {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: #fff;
            background-color: #238636; /* Unban button color */
        }

        .banned-users-table .actions button:hover {
            background-color: #2ea043;
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
            <li><a href="skill_management.php">Skill Management</a></li>
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="location_management.php">Location Management</a></li>
            <li><a href="recruiter_management.php">Recruiter Management</a></li>
            <li><a href="jobseeker_management.php">Job Seeker Management</a></li>
            <li><a href="ban_management.php" class="active">Ban Management</a></li>
            <li><a href="analytics.php">Detailed Analytics</a></li>
            <li><a href="export_data.php">Export Data</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content">
        <h1>Ban Management</h1>

        <?php echo $message; ?>

        <div class="banned-users-list-container">
            <h3>Banned Users</h3>
            <?php if (empty($banned_users)): ?>
                <p>No users are currently banned.</p>
            <?php else: ?>
                <table class="banned-users-table">
                    <thead>
                        <tr>
                            <th>Ban ID</th>
                            <th>User ID</th>
                            <th>User Type</th>
                            <th>User Name</th>
                            <th>Company (if Recruiter)</th>
                            <th>Ban Reason</th>
                            <th>Ban Date</th>
                            <th>Banned By Admin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banned_users as $ban): ?>
                            <tr>
                                <td><?php echo $ban['id']; ?></td>
                                <td><?php echo $ban['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($ban['user_type']); ?></td>
                                <td><?php echo htmlspecialchars($ban['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($ban['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($ban['ban_reason']); ?></td>
                                <td><?php echo $ban['ban_date']; ?></td>
                                <td><?php echo $ban['banned_by_admin_id']; ?></td>
                                <td class="actions">
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="ban_id" value="<?php echo $ban['id']; ?>">
                                        <input type="hidden" name="action" value="unban">
                                        <button type="submit" onclick="return confirm('Are you sure you want to lift the ban for this user?')">Unban</button>
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