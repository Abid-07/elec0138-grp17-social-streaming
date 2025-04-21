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

// Fetch the current details of the video
$query = "SELECT title, description FROM videos WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $video_id, $user_id);
$stmt->execute();
$stmt->bind_result($title, $description);
$stmt->fetch();
$stmt->close();

// If the video does not exist or doesn't belong to the user
if (!$title) {
    echo "Video not found or you're not authorized to edit this video.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = $_POST['title'];
    $new_description = $_POST['description'];

    // Update the video details in the database
    $update_query = "UPDATE videos SET title = ?, description = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssii", $new_title, $new_description, $video_id, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Redirect to the videos page after update
    header("Location: view_videos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Video</title>
</head>
<body>
    <h2>Edit Video</h2>
    <form action="edit_video.php?id=<?php echo $video_id; ?>" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea><br>

        <input type="submit" value="Save Changes">
    </form>

    <a href="view_videos.php">Back to Videos</a>
</body>
</html>
