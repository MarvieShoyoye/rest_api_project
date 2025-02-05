<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';


$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET") {
    // Check if 'id' is provided in the query string
    if (isset($_GET["id"])) {
        $id = intval($_GET["id"]);  // Sanitize input to avoid SQL injection

        // Query to get a specific user by ID
        $sql = "SELECT * FROM users WHERE id = $id";
    } else {
        // Query to get all users if no 'id' is provided
        $sql = "SELECT * FROM users";
    }

    $result = $conn->query($sql);
    $users = [];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}

elseif ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["name"]) || !isset($data["email"]) || !isset($data["password"])) {
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $name = $conn->real_escape_string($data["name"]);
    $email = $conn->real_escape_string($data["email"]);
    $password = password_hash($data["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User created successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}

elseif ($method == "DELETE") {
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

    if ($id <= 0) {
        echo json_encode(["error" => "Invalid user ID"]);
        exit;
    }

    $sql = "DELETE FROM users WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting user"]);
    }
}

$conn->close();
?>
