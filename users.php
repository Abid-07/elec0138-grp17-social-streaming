<?php
session_start();
include "db.php"; // Include your database connection

// Fetch all users from the database
$query = "SELECT id, username FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
</head>
<body>
    <h2>All Users</h2>

    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="profile.php?user_id=<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['username']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <a href="index.php">Go back to Home</a>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
