<?php
header("Access-Control-Allow-Origin: *");  // For development ONLY
header("Content-Type: application/json; charset=UTF-8");

// If supporting more methods (POST, PUT, DELETE), include:
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'db_connect.php';  // Make sure $conn is available


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // If there's an ID parameter, get single
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM trivia WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode($data);
        exit;
    }
    // Otherwise, get all
    $sql = "SELECT * FROM trivia";
    $result = $conn->query($sql);
    $rows = [];
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Parse JSON input
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // 2. Validate
    if (!isset($data['trivia_question'], $data['trivia_answer'], $data['difficulty'], $data['username'])) {
        echo json_encode(["error" => "Missing fields"]);
        exit;
    }

    // 3. Insert into DB
    $stmt = $conn->prepare("INSERT INTO trivia (username, trivia_question, trivia_answer, difficulty)
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi",
        $data['username'],
        $data['trivia_question'],
        $data['trivia_answer'],
        $data['difficulty']
    );
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "insert_id" => $stmt->insert_id]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }
    exit;
}
?>