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

// Check ownership
$stmt = $conn->prepare("SELECT username FROM trivia WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$trivia = $result->fetch_assoc();

if (!$trivia) {
    echo "Trivia question not found.";
    exit;
}

if ($trivia['username'] !== $_SESSION['username']) {
    echo "You are not authorized to delete this trivia question.";
    exit;
}

// Perform delete
$delStmt = $conn->prepare("DELETE FROM trivia WHERE id=?");
$delStmt->bind_param("i", $id);
if ($delStmt->execute()) {
    echo "Trivia question deleted successfully. <a href='index.php'>Back to trivia</a>";
} else {
    echo "Error deleting trivia: " . $delStmt->error;
}
$delStmt->close();
$conn->close();
?>
