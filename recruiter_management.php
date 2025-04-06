<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch all recruiters
$sql_recruiters = "SELECT id, name, company, email, registration_date FROM recruiters ORDER BY registration_date DESC";
$result_recruiters = mysqli_query($conn, $sql_recruiters);
$recruiters = mysqli_fetch_all($result_recruiters, MYSQLI_ASSOC);

// Handle delete action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
    $recruiter_id = mysqli_real_escape_string($conn, $_POST["recruiter_id"]);

    $delete_sql = "DELETE FROM recruiters WHERE id = '$recruiter_id'";
    if (mysqli_query($conn, $delete_sql)) {
        $message = "<div style='color: green;'>Recruiter deleted successfully!</div>";
        // Refetch recruiters
        $result_recruiters = mysqli_query($conn, $sql_recruiters);
        $recruiters = mysqli_fetch_all($result_recruiters, MYSQLI_ASSOC);
    } else {
        $message = "<div style='color: red;'>Error deleting recruiter: " . mysqli_error($conn) . "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Management | Skillera Admin (India Edition)</title>
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

        .recruiters-list-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .recruiters-list-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .recruiters-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .recruiters-list-table th, .recruiters-list-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .recruiters-list-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .recruiters-list-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .recruiters-list-table .actions button {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: #fff;
            background-color: #dc2626; /* Delete button color */
        }

        .recruiters-list-table .actions button:hover {
            background-color: #e34a4a;
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
        <h1>Recruiter Management</h1>

        <?php echo $message; ?>

        <div class="recruiters-list-container">
            <h3>Registered Recruiters</h3>
            <?php if (empty($recruiters)): ?>
                <p>No recruiters have registered yet.</p>
            <?php else: ?>
                <table class="recruiters-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recruiters as $recruiter): ?>
                            <tr>
                                <td><?php echo $recruiter['id']; ?></td>
                                <td><?php echo htmlspecialchars($recruiter['name']); ?></td>
                                <td><?php echo htmlspecialchars($recruiter['company']); ?></td>
                                <td><?php echo htmlspecialchars($recruiter['email']); ?></td>
                                <td><?php echo $recruiter['registration_date']; ?></td>
                                <td class="actions">
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="recruiter_id" value="<?php echo $recruiter['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this recruiter?')">Delete</button>
                                    </form>
                                    <a href="view_recruiter.php?id=<?php echo $recruiter['id']; ?>">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>