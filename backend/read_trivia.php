<?php
session_start();
require 'db_connect.php';

$sql = "SELECT id, username, trivia_question, trivia_answer, difficulty FROM trivia";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<strong>Question:</strong> " . htmlspecialchars($row['trivia_question']) . "<br>";
    echo "<strong>Answer:</strong> " . htmlspecialchars($row['trivia_answer']) . "<br>";
    echo "<strong>Difficulty (1-10):</strong> " . htmlspecialchars($row['difficulty']) . "<br>";
    echo "<strong>Created by:</strong> " . htmlspecialchars($row['username']) . "<br>";

    // If current user is the owner, show Update/Delete links
    if (isset($_SESSION['username']) && $_SESSION['username'] == $row['username']) {
        echo "<a href='update_trivia.php?id=" . $row['id'] . "'>Update</a> | ";
        echo "<a href='delete_trivia.php?id=" . $row['id'] . "'>Delete</a>";
    }
    echo "</div><hr>";
}

$conn->close();
?>
