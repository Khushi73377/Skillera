<?php
session_start();
include("db.php");

if (!isset($_SESSION['recruiter_id'])) {
    header("Location: login-recruiter.html");
    exit();
}

$recruiter_id = $_SESSION['recruiter_id'];

$query = "SELECT name, email, company FROM recruiters WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $recruiter_id);
$stmt->execute();
$result = $stmt->get_result();
$recruiter = $result->fetch_assoc();

// Check for session messages
$message = isset($_SESSION['profile_message']) ? $_SESSION['profile_message'] : '';
$message_type = isset($_SESSION['profile_message_type']) ? $_SESSION['profile_message_type'] : '';

// Clear session messages
unset($_SESSION['profile_message']);
unset($_SESSION['profile_message_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="style_recruiter.css">
    <style>
        .message-box {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <?php include("header.html"); ?>

    <main>
        <h2>Update Profile</h2>

        <?php if (!empty($message)): ?>
            <div class="message-box <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($recruiter['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($recruiter['email']) ?>" required>

            <label for="company">Company Name:</label>
            <input type="text" name="company" id="company" value="<?= htmlspecialchars($recruiter['company']) ?>" required>

            <label for="password">New Password (Optional):</label>
            <input type="password" name="password" id="password">

            <button type="submit">Update Profile</button>
        </form>
    </main>

    <?php include("footer.html"); ?>

</body>
</html>

