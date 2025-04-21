<?php
session_start();
include "db.php"; // Ensure database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user details
$query = "SELECT bio, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($bio, $profile_picture);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    
    <form action="edit_profile_process.php" method="post" enctype="multipart/form-data">
        <label for="bio">Bio:</label><br>
        <textarea id="bio" name="bio"><?php echo htmlspecialchars($bio); ?></textarea><br>

        <label for="profile_picture">Profile Picture:</label><br>
        <?php if ($profile_picture): ?>
            <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Current Profile Picture" width="150"><br>
        <?php endif; ?>
        <input type="file" id="profile_picture" name="profile_picture"><br>

        <input type="submit" value="Save Changes">
    </form>

    <a href="profile.php">Back to Profile</a>
</body>
</html>
