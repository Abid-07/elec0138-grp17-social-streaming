<?php
session_start();
include "db.php"; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the 'video_id' parameter is in the URL
if (!isset($_GET['video_id']) || empty($_GET['video_id'])) {
    die("Video ID not provided.");
}

$video_id = $_GET['video_id']; // Get video ID from the URL parameter

// Fetch the video details (title and price_in_credits)
$query = "SELECT title, price_in_credits FROM videos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $video_id);
$stmt->execute();
$stmt->bind_result($video_title, $price_in_credits);
$stmt->fetch();
$stmt->close();

// If video doesn't exist
if (!$video_title) {
    die("Video not found.");
}

// Check if the user has already purchased this video
$query = "SELECT COUNT(*) FROM purchases WHERE user_id = ? AND video_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $video_id);
$stmt->execute();
$stmt->bind_result($purchase_count);
$stmt->fetch();
$stmt->close();

if ($purchase_count > 0) {
    // User has already purchased the video
    die("You have already purchased this video.");
}

// Fetch user credits
$query = "SELECT credits FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($credits);
$stmt->fetch();
$stmt->close();

// Check if the user has enough credits
if ($credits >= $price_in_credits) {
    // Deduct the credits from the user
    $new_credits = $credits - $price_in_credits;
    $update_credits_query = "UPDATE users SET credits = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_credits_query);
    $update_stmt->bind_param("ii", $new_credits, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Insert the purchase into the purchases table
    $insert_purchase_query = "INSERT INTO purchases (user_id, video_id) VALUES (?, ?)";
    $purchase_stmt = $conn->prepare($insert_purchase_query);
    $purchase_stmt->bind_param("ii", $user_id, $video_id);
    $purchase_stmt->execute();
    $purchase_stmt->close();

    // Redirect to the video comments page with a success message
    header("Location: video_comments.php?id=$video_id&success=Video+Purchased");
    exit;
} else {
    // Not enough credits, show error message
    header("Location: video_comments.php?id=$video_id&error=Not+enough+credits");
    exit;
}
?>

