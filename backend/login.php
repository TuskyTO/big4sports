<?php
session_start();
require 'db_connect.php';

// If already logged in, redirect
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Parameterized query
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashedPasswordFromDB);
    if ($stmt->fetch()) {
        // Check hashed password
        if (password_verify($password, $hashedPasswordFromDB)) {
            $_SESSION['username'] = $username;
            // Create or continue session
            header("Location: index.php"); // main page or quiz overview
            exit;
        }
    }
    // If we reach here, user not found or password mismatch
    echo "Invalid login credentials.";
    $stmt->close();
}
$conn->close();
?>

<title>

    Login

</title>


<form method="POST" action="login.php">
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>

<body>
    If you do not already have an account hre
    <p>If you do not already have an account <a href='register.php'>Click here to Register</a> 
</p>

<body>
    