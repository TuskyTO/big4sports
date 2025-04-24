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

    // Step 1: Get the correct answer
    $stmt = $conn->prepare("SELECT trivia_answer FROM trivia WHERE id = ?");
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
        // Step 2: Insert into user_trivia if not already there
        $checkStmt = $conn->prepare("SELECT id FROM user_trivia WHERE username = ? AND trivia_id = ?");
        $checkStmt->bind_param("si", $username, $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            $insertStmt = $conn->prepare("INSERT INTO user_trivia (username, trivia_id) VALUES (?, ?)");
            $insertStmt->bind_param("si", $username, $id);
            $insertStmt->execute();
            $insertStmt->close();
        }
        $checkStmt->close();

        echo json_encode(["success" => true, "message" => "Correct! The answer is: {$trivia['trivia_answer']}"]);
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
