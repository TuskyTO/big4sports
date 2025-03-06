<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "No trivia question specified.";
    exit;
}

// Fetch the trivia question from the database
$stmt = $conn->prepare("SELECT username, trivia_question, trivia_answer, difficulty FROM trivia WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$trivia = $result->fetch_assoc();

if (!$trivia) {
    echo "Trivia question not found.";
    exit;
}

// Ensure the logged-in user is the owner of the trivia question
if ($trivia['username'] !== $_SESSION['username']) {
    echo "You are not authorized to edit this trivia question.";
    exit;
}

// If form submitted, process update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newQuestion = $_POST['trivia_question'];
    $newAnswer = $_POST['trivia_answer'];
    $newDifficulty = $_POST['difficulty'];

    // Validate difficulty range (must be between 1 and 10)
    if ($newDifficulty < 1 || $newDifficulty > 10) {
        echo "Difficulty must be between 1 and 10.";
        exit;
    }

    $updateStmt = $conn->prepare("UPDATE trivia SET trivia_question=?, trivia_answer=?, difficulty=? WHERE id=?");
    $updateStmt->bind_param("ssii", $newQuestion, $newAnswer, $newDifficulty, $id);
    if ($updateStmt->execute()) {
        echo "Trivia updated successfully! <a href='index.php'>Back to trivia</a>";
    } else {
        echo "Error updating trivia: " . $updateStmt->error;
    }
    $updateStmt->close();
    exit;
}
?>


<form method="POST">
  <label>Trivia Question: 
    <input type="text" name="trivia_question" value="<?php echo htmlspecialchars($trivia['trivia_question']); ?>" required>
  </label><br>

  <label>Answer: 
    <input type="text" name="trivia_answer" value="<?php echo htmlspecialchars($trivia['trivia_answer']); ?>" required>
  </label><br>

  <label>Difficulty (1-10): 
    <input type="number" name="difficulty" min="1" max="10" value="<?php echo htmlspecialchars($trivia['difficulty']); ?>" required>
  </label><br>

  <button type="submit">Update Trivia</button>
</form>
