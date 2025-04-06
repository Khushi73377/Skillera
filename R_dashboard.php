<?php
session_start();

// Restrict access to logged-in recruiters only
if (!isset($_SESSION['user'])) {
    header("Location: login-recruiter.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Dashboard</title>
    <link rel="stylesheet" href="style_recruiter.css">
</head>
<body>

    <main>
        <h1>Welcome, Recruiter <?php echo $_SESSION['user']; ?>!</h1>
        <div class="dashboard-container">
            <div class="dashboard-links">
                <a href="post-job.html" class="dashboard-btn">📌 Post a Job</a>
                <a href="manage-jobs.php" class="dashboard-btn">📝 Manage Jobs</a>
                <a href="search-candidates.php" class="dashboard-btn">🔎 Search Candidates</a>
                <a href="view-applications.php" class="dashboard-btn">📩 View Applications</a>
                <a href="update-profile.php" class="dashboard-btn">👤 Update Profile</a>
                <a href="logout.php" class="dashboard-btn logout-btn">🚪 Logout</a>
            </div>
        </div>
    </main>
</body>
</html>
