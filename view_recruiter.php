<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";
$recruiter_id = null;
$recruiter = null;

// Get the recruiter ID from the URL
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $recruiter_id = mysqli_real_escape_string($conn, $_GET["id"]);

    // Fetch the recruiter data based on the ID
    $sql_select = "SELECT * FROM recruiters WHERE id = '$recruiter_id'";
    $result_select = mysqli_query($conn, $sql_select);

    if (mysqli_num_rows($result_select) == 1) {
        $recruiter = mysqli_fetch_assoc($result_select);
    } else {
        $message = "<div style='color: red;'>Error: Recruiter not found.</div>";
        $recruiter_id = null; // Reset recruiter ID if not found
    }
} else {
    $message = "<div style='color: red;'>Error: Invalid recruiter ID.</div>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recruiter Details | Skillera Admin (India Edition)</title>
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

        .recruiter-details-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .recruiter-details-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .recruiter-details-container p {
            margin-bottom: 10px;
        }

        .recruiter-details-container strong {
            color: #c9d1d9;
            font-weight: bold;
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
            <li><a href="skill_management.php">Skill Management</a></li>
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="location_management.php">Location Management</a></li>
            <li><a href="recruiter_management.php" class="active">Recruiter Management</a></li>
            <li><a href="jobseeker_management.php">Job Seeker Management</a></li>
            <li><a href="ban_management.php">Ban Management</a></li>
            <li><a href="analytics.php">Detailed Analytics</a></li>
            <li><a href="export_data.php">Export Data</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content">
        <h1>Recruiter Details</h1>

        <?php echo $message; ?>

        <?php if ($recruiter): ?>
            <div class="recruiter-details-container">
                <h3>Recruiter Information</h3>
                <p><strong>ID:</strong> <?php echo $recruiter['id']; ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($recruiter['name']); ?></p>
                <p><strong>Company:</strong> <?php echo htmlspecialchars($recruiter['company']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($recruiter['email']); ?></p>
                <p><strong>Registration Date:</strong> <?php echo $recruiter['registration_date']; ?></p>
                </div>
        <?php endif; ?>

        <p><a href="recruiter_management.php" class="back-link">Back to Recruiter Management</a></p>

    </div>

</body>
</html>