<?php
session_start();
include "db.php";

// Fetch users who have uploaded videos
$query = "SELECT u.id, u.username, u.profile_picture 
          FROM users u
          JOIN videos v ON u.id = v.user_id
          GROUP BY u.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$stmt->bind_result($user_id, $username, $profile_picture);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h2>Welcome to the Home Page</h2>

    <h3>Content Creators</h3>
    <ul>
        <?php while ($stmt->fetch()): ?>
            <li>
                <!-- Link to the content creator's profile page -->
                <a href="profile.php?id=<?php echo $user_id; ?>">
                    <?php if ($profile_picture): ?>
                        <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="50">
                    <?php else: ?>
                        <img src="default_profile.jpg" alt="Profile Picture" width="50">
                    <?php endif; ?>
                    <?php echo htmlspecialchars($username); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Login / Logout buttons -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> | <a href="registration.php">Register</a>
    <?php endif; ?>
</body>
</html>
