<?php
require_once __DIR__ . '/../db.php';
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "POST") {
    // Create Admin
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["name"]) || !isset($data["email"]) || !isset($data["password"])) {
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $name = $conn->real_escape_string($data["name"]);
    $email = $conn->real_escape_string($data["email"]);
    $password = password_hash($data["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Admin created successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}

elseif ($method == "GET") {
    // Check if an ID is provided
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

    if ($id > 0) {
        // Get Admin by ID
        $sql = "SELECT * FROM admins WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            echo json_encode($admin);
        } else {
            echo json_encode(["error" => "Admin not found"]);
        }
    } else {
        // Get All Admins
        $sql = "SELECT * FROM admins";
        $result = $conn->query($sql);
        $admins = [];

        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }

        echo json_encode($admins);
    }
}


elseif ($method == "PUT") {
    // Update Admin
    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data["id"]) ? intval($data["id"]) : 0;
    $name = isset($data["name"]) ? $conn->real_escape_string($data["name"]) : null;
    $email = isset($data["email"]) ? $conn->real_escape_string($data["email"]) : null;
    $password = isset($data["password"]) ? password_hash($data["password"], PASSWORD_DEFAULT) : null;

    if ($id <= 0) {
        echo json_encode(["error" => "Invalid admin ID"]);
        exit;
    }

    $sql = "UPDATE admins SET ";

    $fields = [];
    if ($name) $fields[] = "name = '$name'";
    if ($email) $fields[] = "email = '$email'";
    if ($password) $fields[] = "password = '$password'";

    $sql .= implode(", ", $fields);
    $sql .= " WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Admin updated successfully"]);
    } else {
        echo json_encode(["error" => "Error updating admin: " . $conn->error]);
    }
}

elseif ($method == "DELETE") {
    // Delete Admin
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

    if ($id <= 0) {
        echo json_encode(["error" => "Invalid admin ID"]);
        exit;
    }

    $sql = "DELETE FROM admins WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Admin deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting admin"]);
    }
}

$conn->close();
?>
