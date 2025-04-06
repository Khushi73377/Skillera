<?php
include("db.php");
session_start();

if (!isset($_SESSION['recruiter_id'])) {
    header("Location: login-recruiter.html");
    exit();
}

$recruiter_id = $_SESSION['recruiter_id'];

$query = "
    SELECT 
        ja.application_id,
        js.name AS candidate_name,
        js.skills,
        js.resume,
        j.title AS job_title
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.job_id
    JOIN job_seekers js ON ja.seeker_id = js.id
    WHERE j.recruiter_id = $recruiter_id
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Applications</title>
    <link rel="stylesheet" href="style_recruiter.css">
</head>
<body>

    <?php include("header.html"); ?>

    <main>
        <h2>Candidate Applications</h2>
        <table border="1">
            <tr>
                <th>Candidate Name</th>
                <th>Job Title</th>
                <th>Skills</th>
                <th>Resume</th>
                <th>Action</th>
            </tr>

            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['candidate_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['job_title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['skills']) . "</td>";
                    echo "<td><a href='uploads/" . htmlspecialchars($row['resume']) . "' target='_blank'>Download</a></td>";
                    echo "<td><a href='view-candidate.php?id=" . $row['application_id'] . "'>View Profile</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No applications found.</td></tr>";
            }
            ?>
        </table>
    </main>

    <?php include("footer.html"); ?>

</body>
</html>
