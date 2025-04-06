<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch all job seekers
$sql_jobseekers = "SELECT id, name, email, registration_date FROM job_seekers ORDER BY registration_date DESC";
$result_jobseekers = mysqli_query($conn, $sql_jobseekers);
$jobseekers = mysqli_fetch_all($result_jobseekers, MYSQLI_ASSOC);

// Handle delete action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
    $jobseeker_id = mysqli_real_escape_string($conn, $_POST["jobseeker_id"]);

    $delete_sql = "DELETE FROM job_seekers WHERE id = '$jobseeker_id'";
    if (mysqli_query($conn, $delete_sql)) {
        $message = "<div style='color: green;'>Job seeker deleted successfully!</div>";
        // Refetch job seekers
        $result_jobseekers = mysqli_query($conn, $sql_jobseekers);
        $jobseekers = mysqli_fetch_all($result_jobseekers, MYSQLI_ASSOC);
    } else {
        $message = "<div style='color: red;'>Error deleting job seeker: " . mysqli_error($conn) . "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Management | Skillera Admin (India Edition)</title>
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
            background:url('job7.avif') no-repeat center center/cover;

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

        .jobseekers-list-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .jobseekers-list-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .jobseekers-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .jobseekers-list-table th, .jobseekers-list-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .jobseekers-list-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .jobseekers-list-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .jobseekers-list-table .actions button {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: #fff;
            background-color: #dc2626; /* Delete button color */
        }

        .jobseekers-list-table .actions button:hover {
            background-color: #e34a4a;
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
        <h1>Job Seeker Management</h1>

        <?php echo $message; ?>

        <div class="jobseekers-list-container">
            <h3>Registered Job Seekers</h3>
            <?php if (empty($jobseekers)): ?>
                <p>No job seekers have registered yet.</p>
            <?php else: ?>
                <table class="jobseekers-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobseekers as $jobseeker): ?>
                            <tr>
                                <td><?php echo $jobseeker['id']; ?></td>
                                <td><?php echo htmlspecialchars($jobseeker['name']); ?></td>
                                <td><?php echo htmlspecialchars($jobseeker['email']); ?></td>
                                <td><?php echo $jobseeker['registration_date']; ?></td>
                                <td class="actions">
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="jobseeker_id" value="<?php echo $jobseeker['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this job seeker?')">Delete</button>
                                    </form>
                                    <a href="view_jobseeker.php?id=<?php echo $jobseeker['id']; ?>">View Details</a>
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
