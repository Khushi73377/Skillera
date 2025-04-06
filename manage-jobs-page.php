<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="style_recruiter.css">
</head>
<body>

    <?php include("header.php"); ?>  <!-- Ensure it is PHP -->

    <main>
        <h2>Your Job Listings</h2>
        <table>
            <tr>
                <th>Job Title</th>
                <th>Skills</th>
                <th>Location</th>
                <th>Salary</th>
                <th>Actions</th>
            </tr>
            <?php include("manage-jobs.php"); ?> <!-- Corrected path -->
        </table>
    </main>

    <?php include("footer.php"); ?>  <!-- Ensure it is PHP -->

</body>
</html>
