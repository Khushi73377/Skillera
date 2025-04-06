<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

// Fetch jobs based on search filters
$search_query = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skill = $_POST['skill'];
    $location = $_POST['location'];
    $title = $_POST['title'];

    $search_query = "WHERE 1=1";
    if (!empty($skill)) {
        $search_query .= " AND skills_required LIKE '%$skill%'";
    }
    if (!empty($location)) {
        $search_query .= " AND location LIKE '%$location%'";
    }
    if (!empty($title)) {
        $search_query .= " AND title LIKE '%$title%'";
    }
}

$query = "SELECT * FROM jobs $search_query ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Jobs</title>
    <link rel="stylesheet" href="js_style.css">
</head>
<body>
    <div class="container">
        <h2>🔍 Search Jobs</h2>
        <form method="post" class="search-form">
            <input type="text" name="title" placeholder="Job Title">
            <input type="text" name="skill" placeholder="Skill (e.g., PHP, JavaScript)">
            <input type="text" name="location" placeholder="Location">
            <button type="submit">Search</button>
        </form>

        <div class="job-list">
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="job-item">
                        <h3><?= $row['title'] ?></h3>
                        <p><strong>Skills:</strong> <?= $row['skills_required'] ?></p>
                        <p><strong>Location:</strong> <?= $row['location'] ?></p>
                        <p><strong>Salary:</strong> <?= $row['salary_range'] ?></p>
                        <p><?= $row['description'] ?></p>
                        <a href="apply_job.php?job_id=<?= $row['job_id'] ?>" class="apply-btn">Apply Now</a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="no-jobs">🚫 No jobs available for the selected criteria.</p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
