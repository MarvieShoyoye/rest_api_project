<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv; //import Dotenv Class

//Load the env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Access environment variables
$host = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully!";
?>
