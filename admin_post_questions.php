<?php
include("db.php"); // Assuming your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skill = mysqli_real_escape_string($conn, $_POST['skill']);
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $option1 = mysqli_real_escape_string($conn, $_POST['option1']);
    $option2 = mysqli_real_escape_string($conn, $_POST['option2']);
    $option3 = isset($_POST['option3']) ? mysqli_real_escape_string($conn, $_POST['option3']) : '';
    $option4 = isset($_POST['option4']) ? mysqli_real_escape_string($conn, $_POST['option4']) : '';
    $correct_answer = mysqli_real_escape_string($conn, $_POST['correct_answer']);

    // Basic validation
    if (empty($skill) || empty($question) || empty($option1) || empty($option2) || empty($correct_answer)) {
        $error_message = "All required fields must be filled.";
    } else {
        // Store the question in the database
        $query = "INSERT INTO questions (skill, question_text, option1, option2, option3, option4, correct_option)
                  VALUES ('$skill', '$question', '$option1', '$option2', '$option3', '$option4', '$correct_answer')";

        if (mysqli_query($conn, $query)) {
            $success_message = "Question added successfully!";
        } else {
            $error_message = "Error adding question: " . mysqli_error($conn);
        }
    }
}
?>
<?php include("admin_post_questions.html"); ?>