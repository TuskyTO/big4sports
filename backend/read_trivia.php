<?php
session_start();
require 'db_connect.php';

// check for id 
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "No trivia ID provided.";
    exit;
}

// to get just one row
$stmt = $conn->prepare("
    SELECT id, username, trivia_question, trivia_answer, difficulty
    FROM trivia
    WHERE id = ?
");
$stmt->bind_param("i", $id);  
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "No trivia found with ID " . htmlspecialchars($id);
    exit;
}

// Display row
echo "<div>";
echo "<strong>Question:</strong> " . htmlspecialchars($row['trivia_question']) . "<br>";
echo "<strong>Answer:</strong> " . htmlspecialchars($row['trivia_answer']) . "<br>";
echo "<strong>Difficulty (1-10):</strong> " . htmlspecialchars($row['difficulty']) . "<br>";
echo "<strong>Created by:</strong> " . htmlspecialchars($row['username']) . "<br>";

// If the logged-in user is the owner, allow Update/Delete
if (isset($_SESSION['username']) && $_SESSION['username'] === $row['username']) {
    echo "<a href='update_trivia.php?id={$row['id']}'>Update</a> | ";
    echo "<a href='delete_trivia.php?id={$row['id']}'>Delete</a>";
}
echo "</div>";

$stmt->close();
$conn->close();
?>
