<?php
session_start();
require 'db_connect.php'; // Contains $conn with valid DB credentials
?>
<!DOCTYPE html>
<html>
<head>
  <title>Big4Sports</title>
  <meta charset="utf-8">
</head>
<body>

<?php
// Check if user is logged in
if (isset($_SESSION['username'])) {
    echo "<p>You are logged in as: " . htmlspecialchars($_SESSION['username']) . "</p>";
    echo "<p><a href='logout.php'>Logout</a></p>";
} else {
    echo "<p>You are not logged in. <a href='login.php'>Login</a> | <a href='register.php'>Register</a></p>";
}
?>

<h1>Big4Sports Trivia</h1>

<?php
//  If logged in, show a link to add a new trivia question
if (isset($_SESSION['username'])) {
    echo "<p><a href='create_trivia.php'>Add a New Trivia Question</a></p>";
}

//  Query the trivia table
$sql = "SELECT id, username, trivia_question, trivia_answer, difficulty FROM trivia";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Display all columns in an HTML table
    echo "<table border='1' cellpadding='8'>";
    echo "<tr>
            <th>ID</th>
            <th>Username</th>
            <th>Trivia Question</th>
            <th>Trivia Answer</th>
            <th>Difficulty</th>
            <th>Action</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['trivia_question']) . "</td>";
        echo "<td>" . htmlspecialchars($row['trivia_answer']) . "</td>";
        echo "<td>" . htmlspecialchars($row['difficulty']) . "</td>";
        
        // Action column: always show "View"
        echo "<td>";
        echo "<a href='view_trivia.php?id=" . $row['id'] . "'>View</a>";
        
        // If the logged-in user is the quiz owner, show Update/Delete
        if (isset($_SESSION['username']) && $_SESSION['username'] === $row['username']) {
            echo " | <a href='update_trivia.php?id=" . $row['id'] . "'>Update</a>";
            echo " | <a href='delete_trivia.php?id=" . $row['id'] . "'>Delete</a>";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No trivia found.</p>";
}

$conn->close();
?>

</body>
</html>
