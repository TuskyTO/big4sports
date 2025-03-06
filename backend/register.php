<?php
session_start();
require 'db_connect.php';

// 1. If the user is already logged in, redirect them. 
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// 2. If the form has been submitted (method = POST), process the registration.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // 3. Basic validation: make sure none of the fields are empty
    if (empty($username) || empty($password) || empty($password2)) {
        echo "All fields are required!";
        exit; // stop here
    }

    // 4. Check password length
    if (strlen($password) < 10) {
        echo "Password must be at least 10 characters.";
        exit;
    }

    // 5. Check if the two password inputs match
    if ($password !== $password2) {
        echo "Passwords do not match!";
        exit;
    }

    // 6. Check if the username is already taken (using parameterized queries to prevent SQL injection)
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

    // 7. Hash the password using password_hash()
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 8. Insert the new user into the database
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


<h1> Registration </h1>

<body>
    <p>If you do not already have an account <a href='register.php'>Click here to Register</a> 
</p>

<body>

<form method="POST" action="register.php">
  <label>Username: <input type="text" name="username" required></label><br>
  <label>Password (min 10 chars): <input type="password" name="password" required></label><br>
  <label>Confirm Password: <input type="password" name="password2" required></label><br>
  <button type="submit">Register</button>
</form>
