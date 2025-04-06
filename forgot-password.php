<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot_email'])) {
    $email = $_POST['forgot_email'];

    $stmt = $conn->prepare("SELECT id FROM recruiters WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $conn->prepare("UPDATE recruiters SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        $resetLink = "http://yourwebsite.com/reset_password.php?token=" . $token; // Replace with your URL
        $subject = "Password Reset Request";
        $body = "Click the following link to reset your password: " . $resetLink;

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through. Replace with your SMTP server
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'abbca22104_jonita@banasthali.in'; // SMTP username. Replace with your email.
            $mail->Password = 'ukkwakactnkhnech'; // SMTP password. Replace with your email password.
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = 587; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('abbca22104_jonita@banasthali.in', 'skillera'); //Replace with your name and email
            $mail->addAddress($email, 'Recipient Name');

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            $message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Email address not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Skill Era</title>
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
        <h2>Forgot Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message-box <?php echo (strpos($message, 'sent') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="forgot_email" class="input-box" placeholder="Enter your email" required>
            <button type="submit" class="login-btn">Send Reset Link</button>
            <a href="login-recruiter.php" class="forgot-password">Back to Login</a>
        </form>
    </div>

</body>
</html>