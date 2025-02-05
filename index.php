<?php

require_once __DIR__ . '/vendor/autoload.php';


header("Content-Type: application/json");

// Get the requested URL
$request = isset($_GET['url']) ? $_GET['url'] : '';

// Route requests to the correct API file
switch ($request) {
    case 'users':
        require_once 'api/user.php';
        break;
    
    default:
        echo json_encode(["error" => "Invalid endpoint"]);
        break;
}
?>
