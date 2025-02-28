<?php
session_start();
require 'db_connect.php';

// If the user is not logged in, redirect them to login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect form data
    $trivia_question = trim($_POST['trivia_question']);
    $trivia_answer   = trim($_POST['trivia_answer']);
    $difficulty      = trim($_POST['difficulty']);
    $username        = $_SESSION['username']; // from the session

    // ensure fields aren’t empty
    if (empty($trivia_question) || empty($trivia_answer) || empty($difficulty)) {
        echo "All fields are required.";
        exit;
    }

    // Ensure difficulty is between 1 and 10
    
    if (!ctype_digit($difficulty)) {
        echo "Difficulty must be an integer between 1 and 10.";
        exit;
    }
    // Convert to integer and check the range
    $difficulty_int = (int) $difficulty;
    if ($difficulty_int < 1 || $difficulty_int > 10) {
        echo "Difficulty must be between 1 and 10.";
        exit;
    }

    // Insert the new trivia question into the database
    $stmt = $conn->prepare("INSERT INTO trivia (username, trivia_question, trivia_answer, difficulty) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $trivia_question, $trivia_answer, $difficulty_int);

    if ($stmt->execute()) {
        // Success message (could also redirect to index)
        echo "Trivia created successfully. <a href='index.php'>Back to Home</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create a New Trivia Question</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Create a New Trivia Question</h1>
    <form method="POST" action="create_trivia.php">
        <label>Question:
            <input type="text" name="trivia_question" required>
        </label><br><br>

        <label>Answer:
            <input type="text" name="trivia_answer" required>
        </label><br><br>

        <label>Difficulty (1–10):
            <!-- type="number" ensures front-end check;
                 min and max limit the user to 1–10 in most browsers -->
            <input type="number" name="difficulty" min="1" max="10" required>
        </label><br><br>

        <button type="submit">Create</button>
    </form>
</body>
</html>
