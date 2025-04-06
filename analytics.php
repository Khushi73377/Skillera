<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

// Fetch total counts
$sql_total_jobseekers = "SELECT COUNT(*) AS total FROM job_seekers";
$result_total_jobseekers = mysqli_query($conn, $sql_total_jobseekers);
$total_jobseekers_data = mysqli_fetch_assoc($result_total_jobseekers);
$total_jobseekers = $total_jobseekers_data['total'];

$sql_total_recruiters = "SELECT COUNT(*) AS total FROM recruiters";
$result_total_recruiters = mysqli_query($conn, $sql_total_recruiters);
$total_recruiters_data = mysqli_fetch_assoc($result_total_recruiters);
$total_recruiters = $total_recruiters_data['total'];

$sql_total_jobs = "SELECT COUNT(*) AS total FROM jobs";
$result_total_jobs = mysqli_query($conn, $sql_total_jobs);
$total_jobs_data = mysqli_fetch_assoc($result_total_jobs);
$total_jobs = $total_jobs_data['total'];

$sql_total_applications = "SELECT COUNT(*) AS total FROM job_applications";
$result_total_applications = mysqli_query($conn, $sql_total_applications);
$total_applications_data = mysqli_fetch_assoc($result_total_applications);
$total_applications = $total_applications_data['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics | Skillera Admin (India Edition)</title>
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

        .analytics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .analytics-card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .analytics-card h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .analytics-card .count {
            font-size: 2em;
            font-weight: bold;
            color: #c9d1d9;
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
        <h1>Platform Analytics</h1>

        <div class="analytics-container">
            <div class="analytics-card">
                <h3>Total Job Seekers</h3>
                <div class="count"><?php echo $total_jobseekers; ?></div>
            </div>
            <div class="analytics-card">
                <h3>Total Recruiters</h3>
                <div class="count"><?php echo $total_recruiters; ?></div>
            </div>
            <div class="analytics-card">
                <h3>Total Jobs Posted</h3>
                <div class="count"><?php echo $total_jobs; ?></div>
            </div>
            <div class="analytics-card">
                <h3>Total Job Applications</h3>
                <div class="count"><?php echo $total_applications; ?></div>
            </div>
            </div>

        </div>

</body>
</html>