<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's credit balance
$credits_query = "SELECT credits FROM users WHERE id = ?";
$credits_stmt = $conn->prepare($credits_query);
$credits_stmt->bind_param("i", $user_id);
$credits_stmt->execute();
$credits_stmt->bind_result($credits);
$credits_stmt->fetch();
$credits_stmt->close();

// Fetch unread messages count
$unread_messages_query = "SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0";
$unread_messages_stmt = $conn->prepare($unread_messages_query);
$unread_messages_stmt->bind_param("i", $user_id);
$unread_messages_stmt->execute();
$unread_messages_stmt->bind_result($unread_messages_count);
$unread_messages_stmt->fetch();
$unread_messages_stmt->close();

// Fetch videos
$query = "SELECT v.id, v.title, v.filename, v.uploaded_at, v.is_live, u.id AS creator_id, u.username, u.profile_picture
          FROM videos v
          JOIN users u ON u.id = v.user_id
          ORDER BY v.uploaded_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($video_id, $video_title, $video_filename, $uploaded_at, $is_live, $creator_id, $creator_username, $creator_profile_picture);

// Fetch top uploaders
$top_uploaders_query = "
    SELECT u.id, u.username, COUNT(v.id) AS video_count
    FROM users u
    JOIN videos v ON v.user_id = u.id
    GROUP BY u.id
    ORDER BY video_count DESC
    LIMIT 5";
$top_uploaders_stmt = $conn->prepare($top_uploaders_query);
$top_uploaders_stmt->execute();
$top_uploaders_stmt->bind_result($top_user_id, $top_username, $video_count);

$top_uploaders = [];
while ($top_uploaders_stmt->fetch()) {
    $top_uploaders[] = [
        'id' => $top_user_id,
        'username' => $top_username,
        'count' => $video_count
    ];
}
$top_uploaders_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Content Creators</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            background: #333;
            padding: 15px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }
        .main-wrapper {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 40px auto;
            gap: 20px;
            padding: 0 20px;
        }
        .sidebar, .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .sidebar {
            flex: 1 1 250px;
            max-height: fit-content;
        }
        .container {
            flex: 3 1 700px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        .top-list {
            list-style: none;
            padding: 0;
        }
        .top-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .top-list a {
            color: #007bff;
            text-decoration: none;
        }
        .top-list a:hover {
            text-decoration: underline;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        video {
            width: 100%;
            border-radius: 10px;
            margin-top: 10px;
        }
        .live-indicator {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
        .donate-button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
        .donate-button:hover {
            background-color: #218838;
        }
        .credit-box {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.1rem;
            color: #444;
        }
        .video-title {
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="profile.php">My Profile</a>
    <a href="send_message.php">Private Message</a>
    <a href="inbox.php">Inbox<?php if ($unread_messages_count > 0) echo " ($unread_messages_count)"; ?></a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-wrapper">
    <div class="sidebar">
        <h3>📈 Top Uploaders</h3>
        <ul class="top-list">
            <?php foreach ($top_uploaders as $u): ?>
                <li>
                    <a href="profile.php?id=<?php echo $u['id']; ?>">
                        <?php echo htmlspecialchars($u['username']); ?>
                    </a><br>
                    <small><?php echo $u['count']; ?> videos</small>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="container">
        <h2>Welcome to the Platform!</h2>
        <div class="credit-box">
            <strong>Your current credits: <?php echo $credits; ?></strong>
        </div>

        <h3>🎥 Latest Videos</h3>
        <div class="grid-container">
            <?php while ($stmt->fetch()): ?>
                <div class="card">
                    <a href="profile.php?id=<?php echo htmlspecialchars($creator_id); ?>">
                        <img class="profile-img" src="uploads/<?php echo htmlspecialchars($creator_profile_picture ?: 'default_profile.jpg'); ?>" alt="Profile Picture">
                        <strong><?php echo htmlspecialchars($creator_username); ?></strong>
                    </a>
                    <div class="video-title"><?php echo htmlspecialchars($video_title); ?></div>
                    <video controls>
                        <source src="uploads/videos/<?php echo htmlspecialchars($video_filename); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <a href="video_comments.php?id=<?php echo $video_id; ?>">View Video</a>

                    <?php if ($is_live == 1): ?>
                        <div class="live-indicator">LIVE</div>
                        <form method="GET" action="video_comments.php">
                            <input type="hidden" name="id" value="<?php echo $video_id; ?>">
                            <button type="submit" class="donate-button">Donate to Streamer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</body>
</html>

<?php
$stmt->close();
$update_notifications_stmt->close();
$credits_stmt->close();
?>

