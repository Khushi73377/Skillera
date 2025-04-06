<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // To store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email_id'];
    $password = $_POST['password'];

    // Check recruiter table
    $stmt = $conn->prepare("SELECT id, name, password FROM recruiters WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // DEBUG: Display stored password for verification (REMOVE THIS IN PRODUCTION)
        // echo "Stored Password: " . $row['password'] . "<br>";
        // echo "Entered Password: " . $password . "<br>";

        if ($password === $row['password']) { // 🔴 Using plain text comparison
            $_SESSION['recruiter_id'] = $row['id'];
            $_SESSION['recruiter_name'] = $row['name'];
            $_SESSION['user'] = $email;
            echo json_encode(['success' => true, 'redirect' => 'R_dashboard.php']); // Send JSON response
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Incorrect password!']); // Send JSON response
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => '❌ No recruiter account found!']); // Send JSON response
        exit();
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
    <title>Recruiter Login | Skill Era</title>
    <link rel="stylesheet" href="log.css">
    <style>
        .message-box {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
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
        <h2>Recruiter Login</h2>

        <div id="error-message" class="message-box error" style="display: none;"></div>

        <form id="login-form">
            <input type="text" name="email_id" class="input-box" placeholder="Email ID" required>
            <input type="password" name="password" class="input-box" placeholder="Password" required>
            <button type="submit" class="login-btn">Login</button>
            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
        </form>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            fetch('login-recruiter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    document.getElementById('error-message').textContent = data.message;
                    document.getElementById('error-message').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('error-message').textContent = 'An error occurred. Please try again.';
                document.getElementById('error-message').style.display = 'block';
            });
        });
    </script>
</body>
</html>