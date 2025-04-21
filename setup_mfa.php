<?php
require_once 'includes/PHPGangsta/GoogleAuthenticator.php';
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$ga = new PHPGangsta_GoogleAuthenticator();

// Generate new secret
$secret = $ga->createSecret();

// Store secret in DB
$stmt = $conn->prepare("UPDATE users SET mfa_secret = ? WHERE id = ?");
$stmt->bind_param("si", $secret, $user_id);
$stmt->execute();
$stmt->close();

// Generate QR code URL
$qrCodeUrl = $ga->getQRCodeGoogleUrl("SocialStreamingUser-$user_id", $secret);

echo "<h3>Scan this QR Code with Google Authenticator:</h3>";
echo "<img src='$qrCodeUrl'><br>";
echo "<p><strong>Manual code:</strong> $secret</p>";

