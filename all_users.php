<?php
session_start();

// Check if admin is logged in (you'll need to adjust this based on your actual login logic)
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php"); // Redirect to login if not logged in
    exit();
}

include("db.php");

// Fetch all job seekers
$sql_jobseekers = "SELECT id, name, email, registration_date FROM job_seekers";
$result_jobseekers = mysqli_query($conn, $sql_jobseekers);
$job_seekers = mysqli_fetch_all($result_jobseekers, MYSQLI_ASSOC);

// Fetch all recruiters
$sql_recruiters = "SELECT id, company, email, registration_date FROM recruiters";
$result_recruiters = mysqli_query($conn, $sql_recruiters);
$recruiters = mysqli_fetch_all($result_recruiters, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users | Skillera Admin (India Edition)</title>
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

        .user-list-container {
            margin-bottom: 30px;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
        }

        .user-list-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .user-table th, .user-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .user-table th {
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
        <h1>All Users</h1>

        <div class="user-list-container">
            <h3>Job Seekers</h3>
            <?php if (empty($job_seekers)): ?>
                <p>No job seekers found.</p>
            <?php else: ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($job_seekers as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['registration_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="user-list-container">
            <h3>Recruiters</h3>
            <?php if (empty($recruiters)): ?>
                <p>No recruiters found.</p>
            <?php else: ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Company Name</th>
                            <th>Contact Email</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recruiters as $recruiter): ?>
                            <tr>
                                <td><?php echo $recruiter['id']; ?></td>
                                <td><?php echo htmlspecialchars($recruiter['company']); ?></td>
                                <td><?php echo htmlspecialchars($recruiter['email']); ?></td>
                                <td><?php echo $recruiter['registration_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>