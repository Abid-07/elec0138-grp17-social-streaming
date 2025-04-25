<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "db.php";

require_once 'includes/php-jwt/src/JWT.php';
require_once 'includes/php-jwt/src/Key.php';
require_once 'includes/PHPGangsta/GoogleAuthenticator.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT credits, mfa_secret FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($credits, $mfa_secret);
if (!$stmt->fetch()) {
    die("User not found.");
}
$stmt->close();

function validateJWT($token, $secret, $conn) {
    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS512'));
        $payload = (array) $decoded;

        if ($payload['iss'] !== 'trusted_auth_server' || time() > $payload['exp']) {
            return false;
        }

        $jti = $payload['jti'];
        $stmt = $conn->prepare("SELECT jti FROM used_tokens WHERE jti = ?");
        $stmt->bind_param("s", $jti);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return false;
        }

        $insert = $conn->prepare("INSERT INTO used_tokens (jti) VALUES (?)");
        $insert->bind_param("s", $jti);
        $insert->execute();
        $insert->close();

        return true;

    } catch (Exception $e) {
        return false;
    }
}

function validateMFA($secret, $code) {
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->verifyCode($secret, $code, 2);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credit_to_add = $_POST['credits'];
    $jwt_token = $_POST['jwt_token'] ?? '';
    $mfa_code = $_POST['mfa_code'] ?? '';
    $jwt_secret = "your_quantum_safe_key"; // Replace with secure key

    if (!validateJWT($jwt_token, $jwt_secret, $conn)) {
        $error = "❌ Invalid or expired JWT token.";
    } elseif (!validateMFA($mfa_secret, $mfa_code)) {
        $error = "❌ MFA verification failed.";
    } elseif ($credit_to_add > 0) {
        $new_balance = $credits + $credit_to_add;
        $update = $conn->prepare("UPDATE users SET credits = ? WHERE id = ?");
        $update->bind_param("ii", $new_balance, $user_id);
        $update->execute();
        $update->close();
        header("Location: profile.php");
        exit;
    } else {
        $error = "❌ Please enter a valid credit amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Credits (Secure)</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            font-size: 1em;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .helper-links {
            margin-top: 20px;
        }
        .helper-links a {
            display: inline-block;
            margin: 8px 10px;
            color: #007BFF;
            text-decoration: none;
            font-weight: 500;
        }
        .helper-links a:hover {
            text-decoration: underline;
        }
        .return-home {
            margin-top: 20px;
        }
        .home-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .home-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🔒 Buy Credits</h2>
    <p>Current Credits: <strong><?php echo htmlspecialchars($credits); ?></strong></p>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="number" name="credits" placeholder="Enter credits to add" min="1" required>
        <input type="text" name="jwt_token" placeholder="Paste your JWT token" required>
        <input type="text" name="mfa_code" placeholder="Enter MFA code" required>
        <button type="submit">Buy Credits</button>
    </form>

    <div class="helper-links">
        <a href="generate_jwt.php" target="_blank">🧾 Generate New JWT</a>
        <a href="setup_mfa.php" target="_blank">🔐 Setup MFA</a>
    </div>

    <div class="return-home">
        <a href="home.php" class="home-button">🏠 Back to Home</a>
    </div>
</div>
</body>
</html>

