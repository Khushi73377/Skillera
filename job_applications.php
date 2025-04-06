<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

// Assuming you have a table named 'job_applications' with columns:
// application_id, job_id, job_seeker_id, applied_date, resume_file (optional)
// You'll likely need to join with the 'jobs' and 'job_seekers' tables to display more details.

$sql_applications = "SELECT
    ja.application_id,
    j.job_id,
    j.title AS job_title,
    js.id AS job_seeker_id,
    js.name AS job_seeker_name,
    js.email AS job_seeker_email,
    ja.applied_at AS applied_date
    FROM job_applications ja
    INNER JOIN jobs j ON ja.job_id = j.job_id
    INNER JOIN job_seekers js ON ja.seeker_id = js.id
    ORDER BY ja.applied_at DESC";

$result_applications = mysqli_query($conn, $sql_applications);
$applications = mysqli_fetch_all($result_applications, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications | Skillera Admin (India Edition)</title>
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

        .applications-list-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
        }

        .applications-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .applications-table th, .applications-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .applications-table th {
            background-color: #30363d;
            font-weight: bold;
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
        <h1>Job Applications</h1>

        <div class="applications-list-container">
            <?php if (empty($applications)): ?>
                <p>No job applications found.</p>
            <?php else: ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Job ID</th>
                            <th>Job Title</th>
                            <th>Job Seeker ID</th>
                            <th>Job Seeker Name</th>
                            <th>Job Seeker Email</th>
                            <th>Applied Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo $application['application_id']; ?></td>
                                <td><?php echo $application['job_id']; ?></td>
                                <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                <td><?php echo $application['job_seeker_id']; ?></td>
                                <td><?php echo htmlspecialchars($application['job_seeker_name']); ?></td>
                                <td><?php echo htmlspecialchars($application['job_seeker_email']); ?></td>
                                <td><?php echo $application['applied_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>