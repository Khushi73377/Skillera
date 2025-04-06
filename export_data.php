<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Function to export data to CSV
function export_to_csv($filename, $data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    if (!empty($data)) {
        // Output CSV header (first row)
        fputcsv($output, array_keys($data[0]));

        // Output CSV data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit();
}

// Handle export requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["export_type"])) {
    $export_type = mysqli_real_escape_string($conn, $_POST["export_type"]);

    switch ($export_type) {
        case 'jobseekers':
            $sql = "SELECT * FROM job_seekers";
            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            export_to_csv('jobseekers_data.csv', $data);
            break;
        case 'recruiters':
            $sql = "SELECT * FROM recruiters";
            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            export_to_csv('recruiters_data.csv', $data);
            break;
        case 'jobs':
            $sql = "SELECT * FROM jobs";
            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            export_to_csv('jobs_data.csv', $data);
            break;
        case 'applications':
            $sql = "SELECT * FROM job_applications";
            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            export_to_csv('job_applications_data.csv', $data);
            break;
        default:
            $message = "<div style='color: red;'>Error: Invalid export type selected.</div>";
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data | Skillera Admin (India Edition)</title>
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

        .export-options-container {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .export-options-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .export-form label {
            display: block;
            margin-bottom: 10px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .export-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #30363d;
            border-radius: 6px;
            background-color: #0d1117;
            color: #c9d1d9;
            box-sizing: border-box;
            font-size: 16px;
        }

        .export-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background-color: #238636;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .export-form button:hover {
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
        <h1>Export Platform Data</h1>

        <?php echo $message; ?>

        <div class="export-options-container">
            <h3>Choose Data to Export</h3>
            <form method="POST" class="export-form">
                <label for="export_type">Select Data Type:</label>
                <select name="export_type" id="export_type">
                    <option value="">-- Select --</option>
                    <option value="jobseekers">Job Seekers</option>
                    <option value="recruiters">Recruiters</option>
                    <option value="jobs">Jobs</option>
                    <option value="applications">Job Applications</option>
                </select>
                <button type="submit">Export as CSV</button>
            </form>
        </div>

    </div>

</body>
</html>