<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

$query = "SELECT ja.*, j.title, j.location, j.salary_range, j.skills_required, j.expiry_date 
          FROM job_applications ja
          JOIN jobs j ON ja.job_id = j.job_id
          WHERE ja.seeker_id = ?
          ORDER BY ja.applied_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $jobseeker_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applied Jobs</title>
    <style>
        body {
            font-family: Arial;
            background: #f1f4f8;
            padding: 40px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-jobs {
            text-align: center;
            font-size: 17px;
            color: #777;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📄 My Applied Jobs</h2>

        <?php if ($result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Skills Required</th>
                    <th>Salary</th>
                    <th>Applied On</th>
                    <th>Status</th>
                    <th>Skill Test Score</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['skills_required']) ?></td>
                        <td><?= htmlspecialchars($row['salary_range']) ?></td>
                        <td><?= date("d M Y", strtotime($row['applied_at'])) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= is_null($row['skill_test_score']) ? 'Not Taken' : $row['skill_test_score'] . "/10" ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p class="no-jobs">🚫 You haven’t applied for any active jobs yet.</p>
        <?php } ?>

        <a href="search_jobs.php" class="back-link">🔍 Search More Jobs</a>
    </div>
</body>
</html>
