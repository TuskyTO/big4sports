<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        handlePost($conn, $data);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    case 'OPTIONS':
        http_response_code(200);
        exit;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        exit;
}

$conn->close();

function handleGet($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $username = $_GET['username'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("SELECT id, trivia_question, difficulty, username, trivia_answer FROM trivia WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $trivia = $result->fetch_assoc();
        $stmt->close();

        if ($trivia && $username !== $trivia['username']) {
            $checkStmt = $conn->prepare("SELECT id FROM user_trivia WHERE username = ? AND trivia_id = ?");
            $checkStmt->bind_param("si", $username, $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
        
            if ($checkResult->num_rows === 0) {
                unset($trivia['trivia_answer']);
            }
        
            $checkStmt->close();
        }
        

        echo json_encode($trivia);
    } else {
        $sql = "SELECT id, trivia_question, difficulty, username, trivia_answer FROM trivia";
        $result = $conn->query($sql);
        $trivia_list = [];

        while ($row = $result->fetch_assoc()) {
            if ($username !== $row['username']) {
                $checkStmt = $conn->prepare("SELECT id FROM user_trivia WHERE username = ? AND trivia_id = ?");
                $checkStmt->bind_param("si", $username, $row['id']);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
            
                if ($checkResult->num_rows === 0) {
                    unset($row['trivia_answer']);
                }
            
                $checkStmt->close();
            }
            
            $trivia_list[] = $row;
        }

        echo json_encode($trivia_list);
    }
    exit;
}

function handlePost($conn, $data) {
    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing action"]);
        return;
    }

    switch ($data['action']) {
        case 'register':
            handleRegister($conn, $data); break;
        case 'login':
            handleLogin($conn, $data); break;
        case 'create_trivia':
            handleCreateTrivia($conn, $data); break;
        case 'guess':
            handleGuess($conn, $data); break;
        case 'reset_guesses':
            handleResetGuesses($conn, $data);
            break;
        case 'get_user_score':
            handleGetUserScore($conn, $data);
            break;
        default:
            http_response_code(400);
            echo json_encode(["error" => "Invalid action"]);
    }
}

function handlePut($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing id parameter"]);
        return;
    }
    $id = intval($_GET['id']);
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (!isset($data['trivia_question'], $data['trivia_answer'], $data['difficulty'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE trivia SET trivia_question = ?, trivia_answer = ?, difficulty = ? WHERE id = ?");
    $stmt->bind_param("ssii", $data['trivia_question'], $data['trivia_answer'], $data['difficulty'], $id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    $stmt->close();
}

function handleDelete($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing id parameter"]);
        return;
    }

    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM trivia WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    $stmt->close();
}

function handleRegister($conn, $data) {
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(["success" => true]);
    $stmt->close();
}

function handleLogin($conn, $data) {
    $username = $data['username'];
    $password = $data['password'];
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        http_response_code(201);
        echo json_encode(["success" => password_verify($password, $row['password'])]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
    }
    $stmt->close();
}

function handleGuess($conn, $input) {
    $id = intval($input['id']);
    $guess = trim(strtolower($input['guess']));
    $username = $input['username'];

    // Step 1: Get the correct answer AND difficulty
    $stmt = $conn->prepare("SELECT trivia_answer, difficulty FROM trivia WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trivia = $result->fetch_assoc();
    $stmt->close();

    if (!$trivia) {
        http_response_code(404);
        echo json_encode(["error" => "Trivia not found"]);
        return;
    }

    $correct = strtolower(trim($trivia['trivia_answer'])) === $guess;

    if ($correct) {
        $difficulty = intval($trivia['difficulty']); // Points = difficulty

        // Step 2: Insert into user_trivia if not already there
        $checkStmt = $conn->prepare("SELECT id FROM user_trivia WHERE username = ? AND trivia_id = ?");
        $checkStmt->bind_param("si", $username, $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            $insertStmt = $conn->prepare("INSERT INTO user_trivia (username, trivia_id, points) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sii", $username, $id, $difficulty);
            $insertStmt->execute();
            $insertStmt->close();
        }
        $checkStmt->close();

        echo json_encode(["success" => true, "message" => "Correct! The answer is: {$trivia['trivia_answer']}"]);
        // After inserting correct guess, update highest_score if needed
        $scoreStmt = $conn->prepare("SELECT SUM(points) as total_score FROM user_trivia WHERE username = ?");
        $scoreStmt->bind_param("s", $username);
        $scoreStmt->execute();
        $scoreResult = $scoreStmt->get_result();
        $scoreRow = $scoreResult->fetch_assoc();
        $currentScore = intval($scoreRow['total_score']);
        $scoreStmt->close();

        $updateStmt = $conn->prepare("
            UPDATE users
            SET highest_score = ?
            WHERE username = ? AND ? > highest_score
        ");
        $updateStmt->bind_param("isi", $currentScore, $username, $currentScore);
        $updateStmt->execute();
        $updateStmt->close();

    } else {
        echo json_encode(["success" => false, "message" => "Incorrect. Try again!"]);
    }
}


function handleResetGuesses($conn, $data) {
    if (!isset($data['username'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing username"]);
        return;
    }

    $username = $data['username'];

    $stmt = $conn->prepare("DELETE FROM user_trivia WHERE username = ?");
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "All guesses reset."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }

    $stmt->close();
}

function handleCreateTrivia($conn, $data) {
    if (!isset($data['username'], $data['trivia_question'], $data['trivia_answer'], $data['difficulty'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        return;
    }

    $username = $data['username'];
    $trivia_question = $data['trivia_question'];
    $trivia_answer = $data['trivia_answer'];
    $difficulty = intval($data['difficulty']);

    $stmt = $conn->prepare("INSERT INTO trivia (username, trivia_question, trivia_answer, difficulty) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $trivia_question, $trivia_answer, $difficulty);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Trivia created successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create trivia"]);
    }

    $stmt->close();
}

function handleGetUserScore($conn, $data) {
    if (!isset($data['username'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing username"]);
        return;
    }

    $username = $data['username'];

    // Get total current score
    $stmt = $conn->prepare("SELECT COALESCE(SUM(points), 0) AS total_points FROM user_trivia WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $total_points = $row['total_points'] ?? 0;

    // Now ALSO fetch the highest_score from users table
    $highStmt = $conn->prepare("SELECT highest_score FROM users WHERE username = ?");
    $highStmt->bind_param("s", $username);
    $highStmt->execute();
    $highResult = $highStmt->get_result();
    $highRow = $highResult->fetch_assoc();
    $highStmt->close();

    $highest_score = $highRow['highest_score'] ?? 0;

    // Now return both
    echo json_encode([
        "success" => true,
        "total_points" => $total_points,
        "highest_score" => $highest_score
    ]);
}
