<?php
session_start();
require 'db_connect.php';

// If the user is already logged in, redirect them
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// If the form has been submitted process registration.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    //  Make sure none of the fields are empty
    if (empty($username) || empty($password) || empty($password2)) {
        echo "All fields are required!";
        exit; // stop here
    }

    // Check password length
    if (strlen($password) < 10) {
        echo "Password must be at least 10 characters.";
        exit;
    }

    // Check if the two password inputs match
    if ($password !== $password2) {
        echo "Passwords do not match!";
        exit;
    }

    // Check if the username is already taken 
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result(); 
    if ($stmt->num_rows > 0) {
        echo "Username already exists. Please choose another.";
        $stmt->close();
        exit; // stop here
    }
    $stmt->close();

    // Hash the password using password_hash()
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);
    if ($stmt->execute()) {
        // 9. Success message with link to login
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        // 10. Error message if insertion fails
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    exit; // end of POST handling
}
?>

<html>
    <body>
<h1> Registration </h1>



<form method="POST" action="register.php">
  <label>Username: <input type="text" name="username" required></label><br>
  <label>Password (min 10 chars): <input type="password" name="password" required></label><br>
  <label>Confirm Password: <input type="password" name="password2" required></label><br>
  <button type="submit">Register</button>
</form>


    <p>If you already have an account <a href='login.php'>Click here to Login</a> 
</p>

<body>
</html>
