<?php
// Enable CORS for development 
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
    if ($id) {
        $stmt = $conn->prepare("SELECT id, trivia_question, difficulty, username, is_answer_revealed, trivia_answer FROM trivia WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $trivia = $result->fetch_assoc();

        if ($trivia['is_answer_revealed'] == 0) {
            unset($trivia['trivia_answer']);
        }

        echo json_encode($trivia);
        $stmt->close();
    } else {
        $sql = "SELECT id, trivia_question, difficulty, username, is_answer_revealed FROM trivia";
        $result = $conn->query($sql);
        $trivia_list = [];
        while ($row = $result->fetch_assoc()) {
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
            handleRegister($conn, $data);
            break;
        case 'login':
            handleLogin($conn, $data);
            break;
        case 'create_trivia':
            handleCreateTrivia($conn, $data);
            break;
        case 'guess':
            handleGuess($conn, $data);
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
        exit;
    }
    $id = intval($_GET['id']);
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (!isset($data['trivia_question'], $data['trivia_answer'], $data['difficulty'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE trivia SET trivia_question = ?, trivia_answer = ?, difficulty = ? WHERE id = ?");
    $stmt->bind_param("ssii", $data['trivia_question'], $data['trivia_answer'], $data['difficulty'], $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => $stmt->affected_rows > 0]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }
    $stmt->close();
    exit;
}

function handleDelete($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing id parameter"]);
        exit;
    }
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM trivia WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => $stmt->affected_rows > 0]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }
    $stmt->close();
    exit;
}

function handleRegister($conn, $data) {
    if (!isset($data['username'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["error" => "Username already exists"]);
        $check->close();
        return;
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User registered"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }
    $stmt->close();
}

function handleLogin($conn, $data) {
    if (!isset($data['username'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            echo json_encode(["success" => true, "username" => $username]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Invalid credentials"]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["error" => "User not found"]);
    }

    $stmt->close();
}

function handleGuess($conn, $input) {
    if (!isset($input['id'], $input['guess'], $input['username'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing parameters"]);
        return;
    }

    $id = intval($input['id']);
    $guess = $input['guess'];
    $username = $input['username'];

    $stmt = $conn->prepare("SELECT trivia_answer, is_answer_revealed FROM trivia WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trivia = $result->fetch_assoc();

    if (!$trivia) {
        http_response_code(404);
        echo json_encode(["error" => "Trivia not found"]);
        return;
    }

    $is_correct = strtolower(trim($guess)) === strtolower(trim($trivia['trivia_answer']));

    if ($is_correct) {
        $stmt = $conn->prepare("UPDATE trivia SET is_answer_revealed = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Correct! The answer is: " . $trivia['trivia_answer']]);
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect. Try again!"]);
    }

    $stmt->close();
}

function handleCreateTrivia($conn, $data) {
    if (!isset($data['username'], $data['trivia_question'], $data['trivia_answer'], $data['difficulty'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO trivia (username, trivia_question, trivia_answer, difficulty, is_answer_revealed)
                            VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("sssi", $data['username'], $data['trivia_question'], $data['trivia_answer'], $data['difficulty']);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "insert_id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }

    $stmt->close();
}
