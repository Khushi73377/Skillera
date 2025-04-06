<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";
$settings = array();

// Fetch existing settings from the database
$sql_settings = "SELECT setting_name, setting_value FROM settings";
$result_settings = mysqli_query($conn, $sql_settings);
while ($row = mysqli_fetch_assoc($result_settings)) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

// Handle form submission for updating settings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        // Only process settings that are not the submit button
        if ($key != "save_settings") {
            $setting_name = mysqli_real_escape_string($conn, $key);
            $setting_value = mysqli_real_escape_string($conn, $value);

            // Check if the setting already exists, if so, update, otherwise insert
            $check_sql = "SELECT setting_name FROM settings WHERE setting_name = '$setting_name'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                $update_sql = "UPDATE settings SET setting_value = '$setting_value' WHERE setting_name = '$setting_name'";
                if (mysqli_query($conn, $update_sql)) {
                    $message = "<div style='color: green;'>Settings updated successfully!</div>";
                    $settings[$setting_name] = $setting_value; // Update the local array
                } else {
                    $message = "<div style='color: red;'>Error updating setting '$setting_name': " . mysqli_error($conn) . "</div>";
                }
            } else {
                $insert_sql = "INSERT INTO settings (setting_name, setting_value) VALUES ('$setting_name', '$setting_value')";
                if (mysqli_query($conn, $insert_sql)) {
                    $message = "<div style='color: green;'>Setting '$setting_name' saved successfully!</div>";
                    $settings[$setting_name] = $setting_value; // Update the local array
                } else {
                    $message = "<div style='color: red;'>Error saving setting '$setting_name': " . mysqli_error($conn) . "</div>";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Skillera Admin (India Edition)</title>
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

        .settings-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .settings-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .settings-container label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .settings-container input[type="text"],
        .settings-container input[type="email"],
        .settings-container input[type="number"],
        .settings-container textarea {
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

        .settings-container textarea {
            resize: vertical;
        }

        .settings-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #58a6ff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .settings-container button:hover {
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
            <li><a href="manage_homepage.php">Manage Homepage Content</a></li>
            <li><a href="manage_pages.php">Manage Static Pages</a></li>
            <li><a href="blog_management.php">Blog Management</a></li>
            <li><a href="settings.php" class="active">Settings</a></li>
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
        <h1>Site Settings</h1>

        <?php echo $message; ?>

        <div class="settings-container">
            <h3>General Settings</h3>
            <form method="POST" action="">
                <label for="site_title">Site Title:</label>
                <input type="text" name="site_title" id="site_title" value="<?php echo htmlspecialchars(isset($settings['site_title']) ? $settings['site_title'] : ''); ?>">

                <label for="meta_description">Meta Description:</label>
                <textarea name="meta_description" id="meta_description" rows="3"><?php echo htmlspecialchars(isset($settings['meta_description']) ? $settings['meta_description'] : ''); ?></textarea>

                <label for="default_email">Default Admin Email:</label>
                <input type="email" name="default_email" id="default_email" value="<?php echo htmlspecialchars(isset($settings['default_email']) ? $settings['default_email'] : ''); ?>">

                <button type="submit" name="save_settings">Save Settings</button>
            </form>
        </div>

        </div>

</body>
</html>