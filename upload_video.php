<?php

session_start();
include "db.php"; // Ensure this connects to your database

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price_in_credits = $_POST['price_in_credits']; // Get the price from the form

    if (!empty($_FILES['video']['name'])) {
        $target_dir = "uploads/videos/";

        // Ensure the directory exists
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                die("Failed to create upload directory.");
            }
        }

        $file_name = basename($_FILES["video"]["name"]);
        $target_file = $target_dir . $file_name;
        $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Debugging: Check if file was uploaded successfully
        if ($_FILES["video"]["error"] > 0) {
            die("File Upload Error: " . $_FILES["video"]["error"]);
        }

        if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
            // Insert video details into the database, including price_in_credits
            $stmt = $conn->prepare("INSERT INTO videos (user_id, title, description, filename, file_path, price_in_credits) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("issssi", $user_id, $title, $description, $file_name, $target_file, $price_in_credits);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            // Redirect back to the profile page after successful upload
            header("Location: profile.php");
            exit;
        } else {
            die("Error moving uploaded file.");
        }
    } else {
        die("No video file selected.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Video</title>
</head>
<body>
    <h2>Upload Video</h2>
    <form action="upload_video.php" method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br>

        <label for="video">Select Video:</label>
        <input type="file" id="video" name="video" required><br>

        <label for="price_in_credits">Price (in credits):</label>
        <input type="number" id="price_in_credits" name="price_in_credits" min="0" required><br>

        <input type="submit" value="Upload">
    </form>
</body>
</html>

