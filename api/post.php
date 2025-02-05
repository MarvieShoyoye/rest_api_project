<?php
require_once __DIR__ . '/../db.php';
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "POST") {
    // Create Post
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["title"]) || !isset($data["content"]) || !isset($data["user_id"])) {
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $title = $conn->real_escape_string($data["title"]);
    $content = $conn->real_escape_string($data["content"]);
    $user_id = intval($data["user_id"]); // Admin or User ID

    $sql = "INSERT INTO posts (title, content, user_id) VALUES ('$title', '$content', $user_id)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Post created successfully"]);
    } else {
        echo json_encode(["error" => "Error creating post: " . $conn->error]);
    }

} elseif ($method == "GET") {
    // Read Posts (Single or All)
    if (isset($_GET["id"])) {
        $id = intval($_GET["id"]);
        $sql = "SELECT posts.*, admins.name AS author FROM posts INNER JOIN admins ON posts.user_id = admins.id WHERE posts.id = $id";
    } else {
        $sql = "SELECT posts.*, admins.name AS author FROM posts INNER JOIN admins ON posts.user_id = admins.id";
    }

    $result = $conn->query($sql);
    $posts = [];

    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode($posts);

} elseif ($method == "PUT") {
    // Update Post
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["id"])) {
        echo json_encode(["error" => "Post ID is required"]);
        exit;
    }

    $id = intval($data["id"]);
    $fields = [];

    if (isset($data["title"])) $fields[] = "title = '" . $conn->real_escape_string($data["title"]) . "'";
    if (isset($data["content"])) $fields[] = "content = '" . $conn->real_escape_string($data["content"]) . "'";

    if (empty($fields)) {
        echo json_encode(["error" => "No fields to update"]);
        exit;
    }

    $sql = "UPDATE posts SET " . implode(", ", $fields) . " WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Post updated successfully"]);
    } else {
        echo json_encode(["error" => "Error updating post: " . $conn->error]);
    }

} elseif ($method == "DELETE") {
    // Delete Post
    if (!isset($_GET["id"])) {
        echo json_encode(["error" => "Post ID is required"]);
        exit;
    }

    $id = intval($_GET["id"]);
    $sql = "DELETE FROM posts WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Post deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting post"]);
    }
}

$conn->close();
?>
