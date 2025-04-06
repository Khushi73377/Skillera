<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "project";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($username) && !empty($message)) {
        $sql = "INSERT INTO community_posts (username, email, message) VALUES ('$username', '$email', '$message')";
        if ($conn->query($sql) === TRUE) {
            header("Location: community.php"); // Refresh page to show new post
            exit();
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Please enter your name and message.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community & Contact</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #a4acb3, #00f2fe);
            background: url('job8.avif') no-repeat center top/cover;
            background-color: rgb(50, 62, 72);
            text-align: center;
        }
        .navbar {
            display: flex;
            justify-content: left;
            background: rgba(85, 178, 232, 0.3);
            padding: 15px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
            transition: 0.3s;
        }
        .navbar a:hover {
            color: #ff7eb3;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            color: black;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .post {
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="navbar">
        <a href="homePage.html">Home</a>
    </div>
    <div class="container">
        <h2>Community & Contact</h2>
        <p>Share your thoughts or reach out to us.</p>
        
        <form action="community.php" method="POST">
            <input type="text" name="username" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email">
            <textarea name="message" placeholder="Write your message..." required></textarea>
            <button type="submit">Submit</button>
        </form>

        <h3>Community Posts</h3>
        <div class="forum">
            <?php
                // Fetch and display posts from database
                $sql = "SELECT username, message, created_at FROM community_posts ORDER BY created_at DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='post'><strong>" . htmlspecialchars($row['username']) . "</strong><p>" . htmlspecialchars($row['message']) . "</p><small>Posted on: " . $row['created_at'] . "</small></div>";
                    }
                } else {
                    echo "<p>No posts yet. Be the first to share!</p>";
                }

                $conn->close();
            ?>
        </div>
    </div>

</body>
</html>
