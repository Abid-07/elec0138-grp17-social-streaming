<?php
$host = "localhost";
$user = "root"; // Default XAMPP user
$password = ""; // Default XAMPP password (empty)
$database = "social_streaming_db"; // Replace with your actual database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to the database successfully.<br>";
}
?>
