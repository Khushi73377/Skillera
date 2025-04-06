<?php
session_start();
include("db.php");

// ✅ Ensure recruiter is logged in
if (!isset($_SESSION['recruiter_id'])) {
    header("Location: login-recruiter.html");
    exit();
}

$recruiter_id = $_SESSION['recruiter_id'];

// ✅ Ensure job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid job ID.");
}

$job_id = (int)$_GET['id'];

// ✅ Fetch job details
$stmt = $conn->prepare("SELECT title, skills_required, location, salary_range FROM jobs WHERE job_id = ? AND recruiter_id = ?");
$stmt->bind_param("ii", $job_id, $recruiter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Job not found.");
}

$job = $result->fetch_assoc();
$stmt->close();

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $skills = $_POST['skills'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];

    $update_stmt = $conn->prepare("UPDATE jobs SET title = ?, skills_required = ?, location = ?, salary_range = ? WHERE job_id = ? AND recruiter_id = ?");
    $update_stmt->bind_param("ssssii", $title, $skills, $location, $salary, $job_id, $recruiter_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Job updated successfully!'); window.location='manage-jobs.php';</script>";
    } else {
        echo "<script>alert('Error updating job.');</script>";
    }

    $update_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job</title>
    <link rel="stylesheet" href="style_recruiter.css">
</head>
<body>

    <?php include("header.html"); ?>

    <main>
        <h2>Edit Job</h2>
        <form action="" method="POST">
            <label for="title">Job Title:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($job['title']) ?>" required>

            <label for="skills">Skills:</label>
<input type="text" name="skills" id="skills" value="<?= htmlspecialchars($job['skills_required']) ?>" required>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?= htmlspecialchars($job['location']) ?>" required>

            <label for="salary">Salary:</label>
<input type="text" name="salary" id="salary" value="<?= htmlspecialchars($job['salary_range']) ?>" required>

            <button type="submit">Update Job</button>
        </form>
    </main>

    <?php include("footer.html"); ?>

</body>
</html>
