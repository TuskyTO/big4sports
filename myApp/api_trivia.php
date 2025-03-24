<?php
// Enable CORS for development (not safe for production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once '..backend/db_connect.php';

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
        // Preflight request for CORS
        http_response_code(200);
        exit;

    default:
        // Method not supported
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        exit;
}

$conn->close();

/**
 * Handle GET requests.
 *  - e.g., GET /api_trivia.php?id=5 => single record
 *  - e.g., GET /api_trivia.php => all records
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

/**
 * Handle POST requests: create a new record.
 *  - Expects JSON in the request body, e.g.:
 *     {
 *       "username": "Alice",
 *       "trivia_question": "What is 2+2?",
 *       "trivia_answer": "4",
 *       "difficulty": 1
 *     }
 */
function handlePost($conn) {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Basic validation
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
    exit;
}

/**
 * Handle PUT requests: update an existing record.
 *  - Usually requires an `id` param in the query string
 *    e.g. PUT /api_trivia.php?id=5
 *  - Expects JSON in the body for updated fields.
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

/**
 * Handle DELETE requests: remove an existing record.
 *  - Usually requires an `id` param, e.g. DELETE /api_trivia.php?id=5
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
