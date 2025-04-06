
<?php
session_start();
include "db_connect.php"; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM job_seekers WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($password === $row['password']) { // No hashing for now
            $_SESSION['jobseeker_id'] = $row['id'];
            $_SESSION['jobseeker_name'] = $row['name'];
            header("Location: jobseeker_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "No user found!";
    }
}                                                                      
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Login</title>
    <link rel="stylesheet" href="js_style.css">
</head>
<body>
    <div class="login-container">
        <h2>Job Seeker Login</h2>
        <form method="post">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
</body>
</html>
