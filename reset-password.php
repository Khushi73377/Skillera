<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT email, token_expiry FROM recruiters WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $expiry = $row['token_expiry'];
        $email = $row['email'];

        if (strtotime($expiry) > time()) {
            // Token is valid, display password reset form
            if (isset($_POST['new_password'])) {
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];

                if ($newPassword !== $confirmPassword) {
                    $error = "Passwords do not match.";
                } else if (strlen($newPassword) < 8) {
                    $error = "Password must be at least 8 characters long.";
                } else {
                    // Update password in database (without hashing for testing)
                    $stmt = $conn->prepare("UPDATE recruiters SET password = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
                    $stmt->bind_param("ss", $newPassword, $email); // 🔴 Plain text password. REMOVE IN PRODUCTION!
                    if ($stmt->execute()) {
                        $success = "Password reset successfully. You can now <a href='login-recruiter.php'>login</a>.";
                    } else {
                        $error = "Password reset failed. Please try again.";
                    }
                }
            }
            ?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reset Password</title>
                <link rel="stylesheet" href="log.css">
                <style>
                    .message-box {
                        padding: 10px;
                        margin-bottom: 15px;
                        border-radius: 5px;
                        text-align: center;
                        font-size: 14px;
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

                <div class="login-container">
                    <h2>Reset Password</h2>

                    <?php if (isset($success)): ?>
                        <div class="message-box success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="message-box error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <button type="submit">Reset Password</button>
                    </form>
                </div>

            </body>
            </html>

            <?php
        } else {
            echo "Token has expired.";
        }
    } else {
        echo "Invalid token.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>