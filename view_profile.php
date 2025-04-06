<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: jobseeker_login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

// Fetch jobseeker info
$sql = "SELECT * FROM job_seekers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jobseeker_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 60%; margin: 30px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .field { margin-bottom: 15px; }
        .field label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        .field span { display: block; color: #222; }
        .btn { padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background-color: #45a049; }
        .message-box {
            padding: 12px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>My Profile</h1>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="message-box">✅ Changes saved successfully.</div>
    <?php endif; ?>

    <div class="field">
        <label>Full Name:</label>
        <span><?= htmlspecialchars($row['name']) ?></span>
    </div>

    <div class="field">
        <label>Email:</label>
        <span><?= htmlspecialchars($row['email']) ?></span>
    </div>

    <div class="field">
        <label>Phone:</label>
        <span><?= htmlspecialchars($row['phone']) ?></span>
    </div>

    <div class="field">
        <label>Address:</label>
        <span><?= htmlspecialchars($row['address']) ?></span>
    </div>

    <div class="field">
        <label>Skills:</label>
        <span><?= htmlspecialchars($row['skills']) ?></span>
    </div>

    <a href="JS_profile.php" class="btn">Edit Profile</a>
</div>

</body>
</html>
