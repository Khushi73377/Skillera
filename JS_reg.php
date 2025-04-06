<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password']; // No hashing for testing

    // Password validation regex (8+ chars, upper & lowercase, digit, special char)
    $passwordPattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

    if (empty($name) || empty($email) || empty($contact) || empty($password)) {
        $message = "All fields are required.";
        $messageClass = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageClass = "error";
    } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
        $message = "Invalid contact number. It should be 10 digits.";
        $messageClass = "error";
    } elseif (!preg_match($passwordPattern, $password)) {
        $message = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
        $messageClass = "error";
    } else {
        // Check if email or contact already exists
        $check_sql = "SELECT * FROM job_seekers WHERE email = ? OR contact = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $email, $contact);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email or Contact Number already exists! Please use a different one.";
            $messageClass = "error";
        } else {
            // Insert without hashing (for testing)
            $stmt = $conn->prepare("INSERT INTO job_seekers (name, email, contact, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $contact, $password);

            if ($stmt->execute()) {
                $message = "Registration successful! <a href='login.html'>Click here to login</a>.";
                $messageClass = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $messageClass = "error";
            }

            $stmt->close();
        }
        $check_stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobseeker Registration</title>
    <link rel="stylesheet" href="R_reg.css">
    <style>
        .message-box {
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
        }
        .error {
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
        }
        .success {
            background-color: #ddffdd;
            color: #4f8a10;
            border: 1px solid #4f8a10;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Jobseeker Registration</h2>
        
        <?php if (!empty($message)) : ?>
            <div class="message-box <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="JS_reg.php" method="post">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your business email" required>
            </div>

            <div class="input-group">
                <label for="contact">Contact Number</label>
                <input type="tel" id="contact" name="contact" pattern="[0-9]{10}" placeholder="Enter a 10-digit contact number" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <small>Password must have 8+ characters, one uppercase, one lowercase, one number, and one special character.</small>
            </div>

            <div class="input-group">
                <button type="submit">Register</button>
            </div>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.html">Log in</a></p>
        </div>
    </div>
</body>
</html>
