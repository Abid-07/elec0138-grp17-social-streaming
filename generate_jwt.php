<?php
require_once 'includes/php-jwt/src/JWT.php';
require_once 'includes/php-jwt/src/Key.php';

use Firebase\JWT\JWT;

session_start();

if (!isset($_SESSION['user_id'])) {
    die("Login required.");
}

$user_id = $_SESSION['user_id'];
$secret_key = "your_quantum_safe_key"; // Must match the one used in buy_credits.php

$payload = [
    "iss" => "trusted_auth_server",
    "user_id" => $user_id,
    "exp" => time() + 300, // Token expires in 5 minutes
    "jti" => bin2hex(random_bytes(16)) // unique ID for one-time use
];

$jwt = JWT::encode($payload, $secret_key, 'HS512');
echo "<h3>Your temporary JWT token:</h3>";
echo "<textarea rows='5' cols='100'>$jwt</textarea>";

