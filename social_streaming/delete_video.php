<?php
session_start();
include "db.php"; 

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the video ID is passed in the URL
if (!isset($_GET['id'])) {
    echo "No video ID specified!";
    exit;
}

$video_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the video details to verify ownership
$query = "SELECT filename FROM videos WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $video_id, $user_id);
$stmt->execute();
$stmt->bind_result($filename);
$stmt->fetch();
$stmt->close();

// If the video does not exist or doesn't belong to the user
if (!$filename) {
    echo "Video not found or you're not authorized to delete this video.";
    exit;
}

// Delete the video from the database and the file from the server
$delete_query = "DELETE FROM videos WHERE id = ? AND user_id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("ii", $video_id, $user_id);
$delete_stmt->execute();
$delete_stmt->close();

// Remove the video file from the server
$video_path = "uploads/videos/" . $filename;
if (file_exists($video_path)) {
    unlink($video_path);
}

// Redirect to the videos page after deletion
header("Location: view_videos.php");
exit;
?>
