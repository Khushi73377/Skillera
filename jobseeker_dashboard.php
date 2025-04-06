<?php
session_start();
include "db_connect.php"; // Database connection file

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];
$name = $_SESSION['jobseeker_name'];

// You can fetch some relevant dashboard data here if needed
// For example, number of applied jobs, etc.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Dashboard</title>
    <link rel="stylesheet" href="js_style.css">
    <style>
        /* Specific styles for the dashboard layout */
        body {
            display: flex;
            background-color: #f0f2f5; /* Light grey background */
        }

        .sidebar {
            background-color: #343a40; /* Dark sidebar background */
            color: #fff;
            width: 250px;
            padding-top: 20px;
            height: 100vh; /* Make sidebar full height */
            position: fixed; /* Stick to the side */
            left: 0;
            top: 0;
            overflow-y: auto; /* Allow scrolling if content overflows */
        }

        .sidebar h2 {
            padding: 20px;
            text-align: left;
            margin-bottom: 20px;
            color: #fff;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            color: #ddd;
            transition: background-color 0.3s ease;
            text-align: left;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 250px; /* Adjust margin to accommodate sidebar width */
        }

        .dashboard-welcome {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
        }

        .dashboard-welcome h2 {
            margin-top: 0;
            text-align: left;
            color: #28a745; /* Example primary color */
            margin-bottom: 10px;
        }

        /* You can add more specific styling for dashboard widgets/content here */
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Job Seeker</h2>
        <a href="jobseeker_dashboard.php" class="active">Dashboard</a>
        <a href="search_jobs.php">Search Jobs</a>
        <a href="view_profile.php">View Profile</a>
        <a href="applied_jobs.php">Applied Jobs</a>
        <a href="logout.php" class="logout-btn">Logout</a>
        </div>

    <div class="content">
        <div class="dashboard-welcome">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['jobseeker_name']); ?>!</h2>
            </div>

         </div>

</body>
</html>