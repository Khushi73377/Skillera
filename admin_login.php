<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("db.php"); // Ensure your database connection file is included

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_id = mysqli_real_escape_string($conn, $_POST["email_id"]);
    $password = $_POST["password"];

    $sql = "SELECT id, name, password FROM admin WHERE email = '$email_id'";
    error_log("SQL Query: " . $sql); // Log the query

    $result = mysqli_query($conn, $sql);

    if ($result === false) {
        error_log("MySQL Error: " . mysqli_error($conn)); // Log any SQL errors
        $error_message = "Database error. Please try again later.";
    } elseif (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($password === $row["password"]) { // Insecure comparison for now
            $_SESSION["admin_loggedin"] = true;
            $_SESSION["admin_id"] = $row["id"];
            $_SESSION["admin_username"] = $row["name"];
            header("Location: admin_index.html");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "Admin not found with this Email ID.";
    }

    if ($error_message) {
        $_SESSION["login_error"] = $error_message;
        header("Location: admin_login.php"); // Redirect back to the login page to display the error
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Skillera Admin (India Edition)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0d1117;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #161b22;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #58a6ff;
            margin-bottom: 30px;
        }

        .input-box {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #30363d;
            border-radius: 6px;
            background-color: #0d1117;
            color: #c9d1d9;
            box-sizing: border-box;
            font-size: 16px;
        }

        .input-box:focus {
            outline: none;
            border-color: #58a6ff;
            box-shadow: 0 0 5px rgba(88, 166, 255, 0.5);
        }

        .login-btn {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 6px;
            background-color: #238636;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #2ea043;
        }

        .error-message {
            color: #f8d7da;
            background-color: #f5c2c7;
            border: 1px solid #f5c2c7;
            padding: 10px;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="login-container">
        <h2>Admin Login</h2>
        <form action="" method="POST">
            <input type="text" name="email_id" class="input-box" placeholder="Email ID" required>
            <input type="password" name="password" class="input-box" placeholder="Password" required>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <?php
        if (isset($_SESSION["login_error"])) {
            echo '<p class="error-message">' . $_SESSION["login_error"] . '</p>';
            unset($_SESSION["login_error"]); // Clear the error after displaying
        }
        ?>
    </div>

</body>
</html>