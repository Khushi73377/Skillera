<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch existing categories
$sql_categories = "SELECT id, category_name FROM categories ORDER BY category_name ASC";
$result_categories = mysqli_query($conn, $sql_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

// Handle form submission for adding/editing/deleting categories
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        if ($_POST["action"] == "add") {
            $new_category = mysqli_real_escape_string($conn, $_POST["new_category"]);
            if (!empty($new_category)) {
                // Check if category already exists
                $check_sql = "SELECT id FROM categories WHERE category_name = '$new_category'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $message = "<div style='color: red;'>Error: Category '$new_category' already exists.</div>";
                } else {
                    $insert_sql = "INSERT INTO categories (category_name) VALUES ('$new_category')";
                    if (mysqli_query($conn, $insert_sql)) {
                        $message = "<div style='color: green;'>Category '$new_category' added successfully!</div>";
                        // Refetch categories
                        $result_categories = mysqli_query($conn, $sql_categories);
                        $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
                    } else {
                        $message = "<div style='color: red;'>Error adding category: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                $message = "<div style='color: red;'>Error: Category name cannot be empty.</div>";
            }
        } elseif ($_POST["action"] == "edit") {
            $edit_id = mysqli_real_escape_string($conn, $_POST["edit_id"]);
            $edit_category = mysqli_real_escape_string($conn, $_POST["edit_category"]);
            if (!empty($edit_category)) {
                // Check if the updated category name already exists for another category
                $check_sql = "SELECT id FROM categories WHERE category_name = '$edit_category' AND id != '$edit_id'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $message = "<div style='color: red;'>Error: Category '$edit_category' already exists.</div>";
                } else {
                    $update_sql = "UPDATE categories SET category_name = '$edit_category' WHERE id = '$edit_id'";
                    if (mysqli_query($conn, $update_sql)) {
                        $message = "<div style='color: green;'>Category updated successfully!</div>";
                        // Refetch categories
                        $result_categories = mysqli_query($conn, $sql_categories);
                        $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
                    } else {
                        $message = "<div style='color: red;'>Error updating category: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                $message = "<div style='color: red;'>Error: Category name cannot be empty.</div>";
            }
        } elseif ($_POST["action"] == "delete") {
            $delete_id = mysqli_real_escape_string($conn, $_POST["delete_id"]);
            $delete_sql = "DELETE FROM categories WHERE id = '$delete_id'";
            if (mysqli_query($conn, $delete_sql)) {
                $message = "<div style='color: green;'>Category deleted successfully!</div>";
                // Refetch categories
                $result_categories = mysqli_query($conn, $sql_categories);
                $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
            } else {
                $message = "<div style='color: red;'>Error deleting category: " . mysqli_error($conn) . "</div>";
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
    <title>Category Management | Skillera Admin (India Edition)</title>
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

        .add-category-container, .categories-list-container, .edit-category-form {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .add-category-container h3, .categories-list-container h3, .edit-category-form h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .add-category-container label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .add-category-container input[type="text"] {
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

        .add-category-container button {
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

        .add-category-container button:hover {
            background-color: #2ea043;
        }

        .categories-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .categories-list-table th, .categories-list-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .categories-list-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .categories-list-table .actions a, .categories-list-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .categories-list-table .actions a {
            text-decoration: none;
            color: #58a6ff;
            font-size: 14px;
        }

        .categories-list-table .actions a:hover {
            text-decoration: underline;
        }

        .categories-list-table .actions form button {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }

        .categories-list-table .actions form button:hover {
            text-decoration: underline;
        }

        .edit-category-form label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .edit-category-form input[type="text"] {
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

        .edit-category-form button {
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

        .edit-category-form button:hover {
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
            <li><a href="settings.php">Settings</a></li>
            <li><a href="skill_management.php">Skill Management</a></li>
            <li><a href="category_management.php" class="active">Category Management</a></li>
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
        <h1>Category Management</h1>

        <?php echo $message; ?>

        <div class="add-category-container">
            <h3>Add New Category</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <label for="new_category">Category Name:</label>
                <input type="text" name="new_category" id="new_category" required>
                <button type="submit">Add Category</button>
            </form>
        </div>

        <div class="categories-list-container">
            <h3>Existing Categories</h3>
            <?php if (empty($categories)): ?>
                <p>No categories have been added yet.</p>
            <?php else: ?>
                <table class="categories-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                <td class="actions">
                                    <a href="edit_category.php?id=<?php echo $category['id']; ?>">Edit</a>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="delete_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
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