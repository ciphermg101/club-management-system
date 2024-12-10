<?php

// Include the file containing the loadEnv function
require_once __DIR__ . '/envLoader.php';

// Load the .env file
loadEnv(__DIR__ . '/.env');

date_default_timezone_set('Africa/Nairobi');  // Set the timezone globally

// Access the environment variables
$host = $_ENV['DB_HOST'];
$db = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
