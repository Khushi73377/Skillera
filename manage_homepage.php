<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Assuming you have a table to store homepage content, let's call it 'homepage_content'
// It might have columns like 'section_id', 'title', 'content', 'image_path', etc.

// Fetch existing homepage content (you might need to adjust this based on your table structure)
$sql_homepage_content = "SELECT * FROM homepage_content ORDER BY section_id ASC";
$result_homepage_content = mysqli_query($conn, $sql_homepage_content);
$homepage_data = mysqli_fetch_all($result_homepage_content, MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process updates to existing sections
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'title_') === 0) {
            $section_id = substr($key, strlen('title_'));
            $new_title = mysqli_real_escape_string($conn, $value);
            $content_key = 'content_' . $section_id;
            $new_content = isset($_POST[$content_key]) ? mysqli_real_escape_string($conn, $_POST[$content_key]) : '';

            $sql_update = "UPDATE homepage_content SET title = '$new_title', content = '$new_content' WHERE section_id = '$section_id'";
            if (mysqli_query($conn, $sql_update)) {
                $message = "<div style='color: green;'>Homepage content updated successfully!</div>";
                // Refetch data to display updated content
                $result_homepage_content = mysqli_query($conn, $sql_homepage_content);
                $homepage_data = mysqli_fetch_all($result_homepage_content, MYSQLI_ASSOC);
            } else {
                $message = "<div style='color: red;'>Error updating section $section_id: " . mysqli_error($conn) . "</div>";
            }
        }
        // You can add logic here to handle image uploads or other homepage elements
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Homepage Content | Skillera Admin (India Edition)</title>
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

        .homepage-section {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .homepage-section h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .homepage-section label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .homepage-section input[type="text"],
        .homepage-section textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #30363d;
            border-radius: 6px;
            background-color: #0d1117;
            color: #c9d1d9;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }

        .homepage-section textarea {
            resize: vertical;
        }

        .homepage-section .actions button {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #58a6ff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        .homepage-section .actions button:hover {
            background-color: #3d8bf1;
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
            <li><a href="manage_homepage.php" class="active">Manage Homepage Content</a></li>
            <li><a href="manage_pages.php">Manage Static Pages</a></li>
            <li><a href="blog_management.php">Blog Management</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="skill_management.php">Skill Management</a></li>
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="location_management.php">Location Management</a></li>
            <li><a href="recruiter_management.php">Recruiter Management</a></li>
            <li><a href="jobseeker_management.php">Job Seeker Management</a></li>
            <li><a href="ban_management.php">Ban Management</a></li>
            <li><a href="analytics.php">Detailed Analytics</a></li>
            <li><a href="export_data.php">Export Data</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content">
        <h1>Manage Homepage Content</h1>

        <?php echo $message; ?>

        <form method="POST" action="">
            <?php if (empty($homepage_data)): ?>
                <p>No homepage content sections found. You might need to add them to the database first.</p>
            <?php else: ?>
                <?php foreach ($homepage_data as $section): ?>
                    <div class="homepage-section">
                        <h3>Section: <?php echo htmlspecialchars($section['section_id']); ?></h3>
                        <label for="title_<?php echo $section['section_id']; ?>">Title:</label>
                        <input type="text" name="title_<?php echo $section['section_id']; ?>" id="title_<?php echo $section['section_id']; ?>" value="<?php echo htmlspecialchars($section['title']); ?>">

                        <label for="content_<?php echo $section['section_id']; ?>">Content:</label>
                        <textarea name="content_<?php echo $section['section_id']; ?>" id="content_<?php echo $section['section_id']; ?>" rows="5"><?php echo htmlspecialchars($section['content']); ?></textarea>

                        <div class="actions">
                            </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit">Save Homepage Content Changes</button>
            <?php endif; ?>
        </form>

    </div>

</body>
</html>