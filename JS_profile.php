<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

// Fetch jobseeker info
$query = "SELECT * FROM job_seekers WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $jobseeker_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jobseeker Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { width: 60%; margin: 20px auto; background-color: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1, h2 { color: #333; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        section { margin-bottom: 20px; }

        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Jobseeker Profile</h1>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success-box">✅ Changes saved successfully!</div>
    <?php endif; ?>

    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <!-- Personal Information -->
        <section>
            <h2>Personal Information</h2>
            <label for="name">Full Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

            <label for="address">Address:</label>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
        </section>

        <!-- Skills Section -->
        <section>
            <h2>Skills</h2>
            <label>Skills (comma-separated):</label>
            <input type="text" name="skills" value="<?= htmlspecialchars($user['skills']) ?>">
        </section>

        <!-- Job Experience Section -->
        <section>
            <h2>Job Experience</h2>
            <div id="experience-container">
                <!-- Add experience dynamically here -->
            </div>
            <button type="button" id="addExperience">Add Another Job</button>
        </section>

        <!-- File Uploads -->
        <section>
            <h2>Resume</h2>
            <label>Upload Resume (PDF only):</label>
            <input type="file" name="resume">
        </section>

        <button type="submit">Save Changes</button>
    </form>
</div>

<script>
document.getElementById('addExperience').addEventListener('click', function() {
    const container = document.getElementById('experience-container');
    container.innerHTML += `
        <label>Job Title:</label><input type="text" name="jobTitle[]" required>
        <label>Company Name:</label><input type="text" name="company[]" required>
        <label>Job Duration:</label><input type="text" name="duration[]" required>
    `;
});
</script>

</body>
</html>
