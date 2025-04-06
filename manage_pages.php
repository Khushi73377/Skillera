<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch existing static pages
$sql_pages = "SELECT id, title, slug FROM static_pages ORDER BY title ASC";
$result_pages = mysqli_query($conn, $sql_pages);
$pages = mysqli_fetch_all($result_pages, MYSQLI_ASSOC);

// Handle form submission for adding/editing pages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        if ($_POST["action"] == "add") {
            $new_title = mysqli_real_escape_string($conn, $_POST["new_title"]);
            $new_slug = mysqli_real_escape_string($conn, $_POST["new_slug"]);
            $new_content = mysqli_real_escape_string($conn, $_POST["new_content"]);

            // Basic slug validation (you might want more robust validation)
            $new_slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower($new_slug));

            // Check if slug already exists
            $check_slug_sql = "SELECT id FROM static_pages WHERE slug = '$new_slug'";
            $check_slug_result = mysqli_query($conn, $check_slug_sql);
            if (mysqli_num_rows($check_slug_result) > 0) {
                $message = "<div style='color: red;'>Error: Slug '$new_slug' already exists. Please choose a different one.</div>";
            } else {
                $insert_sql = "INSERT INTO static_pages (title, slug, content, created_at, updated_at) VALUES ('$new_title', '$new_slug', '$new_content', NOW(), NOW())";
                if (mysqli_query($conn, $insert_sql)) {
                    $message = "<div style='color: green;'>Static page '$new_title' added successfully!</div>";
                    // Refetch pages
                    $result_pages = mysqli_query($conn, $sql_pages);
                    $pages = mysqli_fetch_all($result_pages, MYSQLI_ASSOC);
                } else {
                    $message = "<div style='color: red;'>Error adding page: " . mysqli_error($conn) . "</div>";
                }
            }
        } elseif ($_POST["action"] == "edit") {
            $page_id = mysqli_real_escape_string($conn, $_POST["edit_id"]);
            $edit_title = mysqli_real_escape_string($conn, $_POST["edit_title"]);
            $edit_slug = mysqli_real_escape_string($conn, $_POST["edit_slug"]);
            $edit_content = mysqli_real_escape_string($conn, $_POST["edit_content"]);

            $edit_slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower($edit_slug));

            // Check if the new slug already exists for a different page
            $check_slug_sql = "SELECT id FROM static_pages WHERE slug = '$edit_slug' AND id != '$page_id'";
            $check_slug_result = mysqli_query($conn, $check_slug_sql);
            if (mysqli_num_rows($check_slug_result) > 0) {
                $message = "<div style='color: red;'>Error: Slug '$edit_slug' already exists for another page. Please choose a different one.</div>";
            } else {
                $update_sql = "UPDATE static_pages SET title = '$edit_title', slug = '$edit_slug', content = '$edit_content', updated_at = NOW() WHERE id = '$page_id'";
                if (mysqli_query($conn, $update_sql)) {
                    $message = "<div style='color: green;'>Static page '$edit_title' updated successfully!</div>";
                    // Refetch pages
                    $result_pages = mysqli_query($conn, $sql_pages);
                    $pages = mysqli_fetch_all($result_pages, MYSQLI_ASSOC);
                } else {
                    $message = "<div style='color: red;'>Error updating page: " . mysqli_error($conn) . "</div>";
                }
            }
        } elseif ($_POST["action"] == "delete") {
            $delete_id = mysqli_real_escape_string($conn, $_POST["delete_id"]);
            $delete_sql = "DELETE FROM static_pages WHERE id = '$delete_id'";
            if (mysqli_query($conn, $delete_sql)) {
                $message = "<div style='color: green;'>Static page deleted successfully!</div>";
                // Refetch pages
                $result_pages = mysqli_query($conn, $sql_pages);
                $pages = mysqli_fetch_all($result_pages, MYSQLI_ASSOC);
            } else {
                $message = "<div style='color: red;'>Error deleting page: " . mysqli_error($conn) . "</div>";
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
    <title>Manage Static Pages | Skillera Admin (India Edition)</title>
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

        .add-page-container, .edit-page-container, .pages-list-container {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .add-page-container h3, .edit-page-container h3, .pages-list-container h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .add-page-container label, .edit-page-container label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .add-page-container input[type="text"], .add-page-container textarea,
        .edit-page-container input[type="text"], .edit-page-container textarea {
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

        .add-page-container textarea, .edit-page-container textarea {
            resize: vertical;
            min-height: 150px;
        }

        .add-page-container button, .edit-page-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #238636;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        .add-page-container button:hover, .edit-page-container button:hover {
            background-color: #2ea043;
        }

        .pages-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .pages-list-table th, .pages-list-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .pages-list-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .pages-list-table .actions a, .pages-list-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .pages-list-table .actions a {
            text-decoration: none;
            color: #58a6ff;
            font-size: 14px;
        }

        .pages-list-table .actions a:hover {
            text-decoration: underline;
        }

        .pages-list-table .actions form button {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }

        .pages-list-table .actions form button:hover {
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
            <li><a href="manage_pages.php" class="active">Manage Static Pages</a></li>
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
        <h1>Manage Static Pages</h1>

        <?php echo $message; ?>

        <div class="add-page-container">
            <h3>Add New Static Page</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <label for="new_title">Title:</label>
                <input type="text" name="new_title" id="new_title" required>

                <label for="new_slug">Slug (URL-friendly, e.g., about-us):</label>
                <input type="text" name="new_slug" id="new_slug" required>

                <label for="new_content">Content:</label>
                <textarea name="new_content" id="new_content" rows="10"></textarea>

                <button type="submit">Add Page</button>
            </form>
        </div>

        <div class="pages-list-container">
            <h3>Existing Static Pages</h3>
            <?php if (empty($pages)): ?>
                <p>No static pages have been created yet.</p>
            <?php else: ?>
                <table class="pages-list-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($page['title']); ?></td>
                                <td><?php echo htmlspecialchars($page['slug']); ?></td>
                                <td class="actions">
                                    <a href="edit_page.php?id=<?php echo $page['id']; ?>">Edit</a>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="delete_id" value="<?php echo $page['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
                                    </form>
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