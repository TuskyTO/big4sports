<?php
// Enable CORS for development 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once 'db_connect.php';

// Determine the request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;

    case 'POST':
        handlePost($conn);
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
        // Not supported
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        exit;
}

$conn->close();

/*
 * Handle GET requests.
 */
function handleGet($conn) {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM trivia WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo json_encode($row);
        $stmt->close();
        exit;
    } else {
        $sql = "SELECT * FROM trivia";
        $result = $conn->query($sql);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        exit;
    }
}

/*
 * Handle POST requests
 */
function handlePost($conn) {
    $input = file_get_contents("php://input");
    file_put_contents("log.txt", "Received POST Data: " . $input . "\n", FILE_APPEND); // Debugging

    $data = json_decode($input, true);

    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing action"]);
        exit;
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
        default:
            http_response_code(400);
            echo json_encode(["error" => "Invalid action"]);
    }
}



/*
 * Handle PUT requests: update an existing record
 */
function handlePut($conn) {
    // Check if we have an id in the URL
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing id parameter"]);
        exit;
    }
    $id = intval($_GET['id']);

    // Parse the JSON body
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Basic validation (adjust as needed)
    if (!isset($data['trivia_question'], $data['trivia_answer'], $data['difficulty'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    // Example: update question, answer, difficulty
    $stmt = $conn->prepare("UPDATE trivia
                            SET trivia_question = ?, trivia_answer = ?, difficulty = ?
                            WHERE id = ?");
    $stmt->bind_param(
        "ssii",
        $data['trivia_question'],
        $data['trivia_answer'],
        $data['difficulty'],
        $id
    );

    if ($stmt->execute()) {
        // Check if any row was actually updated (stmt->affected_rows)
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "No rows updated"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }
    $stmt->close();
    exit;
}

/*
 * Handle DELETE requests: remove an existing record.
*/
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
        // Check if any row was actually deleted
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "No record found with that id"]);
        }
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
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Secure password

    // Check if username already exists
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
            echo json_encode(["error" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["error" => "User not found"]);
    }

    $stmt->close();
}


function handleCreateTrivia($conn, $data) {
    if (!isset($data['username'], $data['trivia_question'], $data['trivia_answer'], $data['difficulty'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO trivia (username, trivia_question, trivia_answer, difficulty)
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param(
        "sssi",
        $data['username'],
        $data['trivia_question'],
        $data['trivia_answer'],
        $data['difficulty']
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "insert_id" => $stmt->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }

    $stmt->close();
}
