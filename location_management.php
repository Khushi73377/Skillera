<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include("db.php");

$message = "";

// Fetch existing locations
$sql_locations = "SELECT id, location_name FROM locations ORDER BY location_name ASC";
$result_locations = mysqli_query($conn, $sql_locations);
$locations = mysqli_fetch_all($result_locations, MYSQLI_ASSOC);

// Handle form submission for adding/editing/deleting locations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        if ($_POST["action"] == "add") {
            $new_location = mysqli_real_escape_string($conn, $_POST["new_location"]);
            if (!empty($new_location)) {
                // Check if location already exists
                $check_sql = "SELECT id FROM locations WHERE location_name = '$new_location'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $message = "<div style='color: red;'>Error: Location '$new_location' already exists.</div>";
                } else {
                    $insert_sql = "INSERT INTO locations (location_name) VALUES ('$new_location')";
                    if (mysqli_query($conn, $insert_sql)) {
                        $message = "<div style='color: green;'>Location '$new_location' added successfully!</div>";
                        // Refetch locations
                        $result_locations = mysqli_query($conn, $sql_locations);
                        $locations = mysqli_fetch_all($result_locations, MYSQLI_ASSOC);
                    } else {
                        $message = "<div style='color: red;'>Error adding location: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                $message = "<div style='color: red;'>Error: Location name cannot be empty.</div>";
            }
        } elseif ($_POST["action"] == "edit") {
            $edit_id = mysqli_real_escape_string($conn, $_POST["edit_id"]);
            $edit_location = mysqli_real_escape_string($conn, $_POST["edit_location"]);
            if (!empty($edit_location)) {
                // Check if the updated location name already exists for another location
                $check_sql = "SELECT id FROM locations WHERE location_name = '$edit_location' AND id != '$edit_id'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $message = "<div style='color: red;'>Error: Location '$edit_location' already exists.</div>";
                } else {
                    $update_sql = "UPDATE locations SET location_name = '$edit_location' WHERE id = '$edit_id'";
                    if (mysqli_query($conn, $update_sql)) {
                        $message = "<div style='color: green;'>Location updated successfully!</div>";
                        // Refetch locations
                        $result_locations = mysqli_query($conn, $sql_locations);
                        $locations = mysqli_fetch_all($result_locations, MYSQLI_ASSOC);
                    } else {
                        $message = "<div style='color: red;'>Error updating location: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                $message = "<div style='color: red;'>Error: Location name cannot be empty.</div>";
            }
        } elseif ($_POST["action"] == "delete") {
            $delete_id = mysqli_real_escape_string($conn, $_POST["delete_id"]);
            $delete_sql = "DELETE FROM locations WHERE id = '$delete_id'";
            if (mysqli_query($conn, $delete_sql)) {
                $message = "<div style='color: green;'>Location deleted successfully!</div>";
                // Refetch locations
                $result_locations = mysqli_query($conn, $sql_locations);
                $locations = mysqli_fetch_all($result_locations, MYSQLI_ASSOC);
            } else {
                $message = "<div style='color: red;'>Error deleting location: " . mysqli_error($conn) . "</div>";
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
    <title>Location Management | Skillera Admin (India Edition)</title>
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

        .add-location-container, .locations-list-container, .edit-location-form {
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            background-color: #161b22;
            margin-bottom: 20px;
        }

        .add-location-container h3, .locations-list-container h3, .edit-location-form h3 {
            color: #58a6ff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .add-location-container label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .add-location-container input[type="text"] {
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

        .add-location-container button {
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

        .add-location-container button:hover {
            background-color: #2ea043;
        }

        .locations-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .locations-list-table th, .locations-list-table td {
            padding: 10px;
            border-bottom: 1px solid #30363d;
            text-align: left;
        }

        .locations-list-table th {
            background-color: #30363d;
            font-weight: bold;
        }

        .locations-list-table .actions a, .locations-list-table .actions form {
            display: inline-block;
            margin-right: 5px;
        }

        .locations-list-table .actions a {
            text-decoration: none;
            color: #58a6ff;
            font-size: 14px;
        }

        .locations-list-table .actions a:hover {
            text-decoration: underline;
        }

        .locations-list-table .actions form button {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }

        .locations-list-table .actions form button:hover {
            text-decoration: underline;
        }

        .edit-location-form label {
            display: block;
            margin-bottom: 5px;
            color: #c9d1d9;
            font-weight: bold;
        }

        .edit-location-form input[type="text"] {
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

        .edit-location-form button {
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

        .edit-location-form button:hover {
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
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="location_management.php" class="active">Location Management</a></li>
            <li><a href="recruiter_management.php">Recruiter Management</a></li>
            <li><a href="jobseeker_management.php">Job Seeker Management</a></li>
            <li><a href="ban_management.php">Ban Management</a></li>
            <li><a href="analytics.php">Detailed Analytics</a></li>
            <li><a href="export_data.php">Export Data</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content">
        <h1>Location Management</h1>

        <?php echo $message; ?>

        <div class="add-location-container">
            <h3>Add New Location</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <label for="new_location">Location Name:</label>
                <input type="text" name="new_location" id="new_location" required>
                <button type="submit">Add Location</button>
            </form>
        </div>

        <div class="locations-list-container">
            <h3>Existing Locations</h3>
            <?php if (empty($locations)): ?>
                <p>No locations have been added yet.</p>
            <?php else: ?>
                <table class="locations-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Location Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $location): ?>
                            <tr>
                                <td><?php echo $location['id']; ?></td>
                                <td><?php echo htmlspecialchars($location['location_name']); ?></td>
                                <td class="actions">
                                    <a href="edit_location.php?id=<?php echo $location['id']; ?>">Edit</a>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="delete_id" value="<?php echo $location['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this location?')">Delete</button>
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