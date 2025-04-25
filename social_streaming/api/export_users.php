<?php
// File: export_users.php
header("Content-Type: application/json");

// Simulated weak key protection — you can harden this later
if (!isset($_GET['key']) || $_GET['key'] !== 'dev123') {
    echo json_encode(["error" => "Access denied."]);
    exit;
}

// Database connection
$host = "localhost";
$user = "root"; // default for XAMPP
$password = "";
$dbname = "social_streaming_db";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

// Laplace noise generator for differential privacy
function laplaceNoise($scale) {
    $u = mt_rand() / mt_getrandmax() - 0.5;
    return -$scale * (($u < 0) ? 1 : -1) * log(1 - 2 * abs($u));
}

// Privacy config
$epsilon = 0.3; // Smaller = more privacy
$sensitivity = 1;
$scale = $sensitivity / $epsilon;

// Query all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Remove raw sensitive fields
        unset($row['password']);
        unset($row['email']); // Or mask if needed

        // Apply DP to preferred_genre
        if (isset($row['preferred_genre'])) {
            if (abs(laplaceNoise($scale)) > 1) {
                $genres = ['Horror', 'Comedy', 'Action', 'Drama', 'Sci-Fi', 'Romance', 'Thriller'];
                $row['preferred_genre'] = $genres[array_rand($genres)];
            }
        }

        // Optional: apply DP to other soft identifiers
        
        if (isset($row['country']) && abs(laplaceNoise($scale)) > 1) {
            $row['country'] = 'Unknown';
        }

        if (isset($row['subscription_type']) && abs(laplaceNoise($scale)) > 1) {
            $row['subscription_type'] = 'Standard'; // default fallback
        }
        

        $users[] = $row;
    }

    echo json_encode($users, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "No users found"]);
}

$conn->close();
?>
