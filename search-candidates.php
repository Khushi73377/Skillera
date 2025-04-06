<?php
include("db.php");

// Get filter values
$skill = isset($_GET['skill']) ? $_GET['skill'] : "";
$experience = isset($_GET['experience']) ? $_GET['experience'] : "";
$location = isset($_GET['location']) ? $_GET['location'] : "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Candidates</title>
    <link rel="stylesheet" href="style_recruiter.css">
</head>
<body>

<?php include("header.html"); ?>

<main>
    <h2>Search & Filter Candidates</h2>

    <form method="GET" action="">
        <label for="skill">Skill:</label>
        <input type="text" name="skill" id="skill" placeholder="e.g., PHP, JavaScript">

        <label for="experience">Experience (Years):</label>
        <input type="number" name="experience" id="experience" min="0">

        <label for="location">Location:</label>
        <input type="text" name="location" id="location" placeholder="City or Country">

        <button type="submit">Search</button>
    </form>

    <h3>Candidate Results</h3>

    <table border="1">
        <tr>
            <th>Name</th>
            <th>Skills</th>
            <th>Contact</th>
            <th>Location</th>
            <th>Resume</th>
            <th>Action</th>
        </tr>

        <?php
        // Build query based on filters
        $query = "SELECT * FROM job_seekers WHERE 1";

        if (!empty($skill)) {
            $query .= " AND skills LIKE '%$skill%'";
        }
        if (!empty($location)) {
            $query .= " AND address LIKE '%$location%'";
        }

        // Experience is not in your table, skip filter

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['skills']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                echo "<td><a href='resumes/" . htmlspecialchars($row['resume']) . "' target='_blank'>View</a></td>";
                echo "<td><a href='view-candidate.php?id=" . $row['id'] . "'>View Profile</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No candidates found.</td></tr>";
        }
        ?>
    </table>
</main>

<?php include("footer.html"); ?>

</body>
</html>
