<?php
session_start();
include("db.php");

// ✅ Ensure recruiter is logged in
if (!isset($_SESSION['recruiter_id'])) {
    header("Location: login-recruiter.html");
    exit();
}

// ✅ Retrieve recruiter ID from session
$recruiter_id = (int)$_SESSION['recruiter_id'];

// ✅ Fetch job listings from the database
$stmt = $conn->prepare("SELECT job_id, title, skills_required, location, salary_range FROM jobs WHERE recruiter_id = ?"); // Corrected column names
$stmt->bind_param("i", $recruiter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="style_recruiter.css">
</head>
<body>

    <?php include("header.html"); ?>

    <main>
        <h2>Your Job Listings</h2>
        <table border="1">
            <tr>
                <th>Job Title</th>
                <th>Skills</th>
                <th>Location</th>
                <th>Salary</th>
                <th>Actions</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['skills_required']) . "</td>"; // Corrected column name
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['salary_range']) . "</td>"; // Corrected column name
                    echo "<td>
                                <a href='edit-job.php?id=" . $row['job_id'] . "'>Edit</a> | 
                                <a href='delete-job.php?id=" . $row['job_id'] . "' onclick='return confirm(\"Are you sure you want to delete this job?\")'>Delete</a>
                            </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; color:red;'>No jobs found.</td></tr>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </table>
    </main>

    <?php include("footer.html"); ?>

</body>
</html>