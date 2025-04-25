<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$view_user_id = isset($_GET['id']) ? $_GET['id'] : $user_id;

$query = "SELECT username, email, bio, profile_picture, credits FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $view_user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $credits);
$stmt->fetch();
$stmt->close();

$is_own_profile = ($view_user_id == $user_id);

$video_query = "SELECT id, title, filename, price_in_credits FROM videos WHERE user_id = ?";
$video_stmt = $conn->prepare($video_query);
$video_stmt->bind_param("i", $view_user_id);
$video_stmt->execute();
$video_stmt->store_result();
$video_stmt->bind_result($video_id, $video_title, $video_filename, $price_in_credits);

$purchased_query = "SELECT video_id FROM purchases WHERE user_id = ?";
$purchased_stmt = $conn->prepare($purchased_query);
$purchased_stmt->bind_param("i", $user_id);
$purchased_stmt->execute();
$purchased_stmt->store_result();
$purchased_stmt->bind_result($purchased_id);

$purchased_videos = [];
while ($purchased_stmt->fetch()) {
    $purchased_videos[] = $purchased_id;
}
$purchased_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($is_own_profile ? "Your Profile" : $username . "'s Profile"); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .profile-card {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
        }
        .profile-card p {
            margin: 8px 0;
            color: #444;
        }
        .credits {
            font-weight: bold;
            color: #28a745;
        }
        .btn-buy-credits {
            background: #007bff;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin-top: 10px;
        }
        .btn-buy-credits:hover {
            background: #0056b3;
        }
        .profile-links {
            text-align: center;
            margin: 20px 0;
        }
        .profile-links a {
            display: inline-block;
            margin: 6px;
            background: #007bff;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
        }
        .profile-links a:hover {
            background-color: #0056b3;
        }
        .videos {
            margin-top: 30px;
        }
        .videos h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .video-grid {
	    display: flex;
	    flex-wrap: wrap;
	    justify-content: center;
	    gap: 20px;
	}
        .video-item {
            background: #fff;
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            width: 100%;
	    max-width: 280px;
	    flex: 1 1 250px;
        }
        .video-item p {
            font-weight: bold;
            margin-bottom: 8px;
        }
        video {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .locked-overlay, .purchased-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .locked-overlay {
            background: rgba(0, 0, 0, 0.6);
        }
        .purchased-overlay {
            background: rgba(0, 128, 0, 0.7);
        }
        .price-tag {
            background: rgba(255, 215, 0, 0.9);
            padding: 6px 12px;
            border-radius: 5px;
            margin-top: 5px;
        }
        .purchase-btn {
            background: #ffc107;
            color: black;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            z-index: 10;
            position: relative;
        }
        .purchase-btn:hover {
            background: #e0a800;
        }
        .view-comments-btn {
            display: inline-block;
            margin-top: 10px;
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
        .view-comments-btn:hover {
            background: #117a8b;
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
    <h2><?php echo htmlspecialchars($is_own_profile ? "Your Profile" : $username . "'s Profile"); ?></h2>

    <!-- Profile Info -->
    <div class="profile-card">
        <?php if ($profile_picture): ?>
            <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        <?php else: ?>
            <p>No profile picture uploaded.</p>
        <?php endif; ?>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Bio:</strong> <?php echo htmlspecialchars($bio ? $bio : "No bio added yet."); ?></p>
        <p class="credits"><strong>Credits:</strong> <?php echo htmlspecialchars($credits); ?></p>
        <?php if ($is_own_profile): ?>
            <a href="buy_credits.php" class="btn-buy-credits">Buy Credits</a>
        <?php endif; ?>
    </div>

    <!-- Profile Actions -->
    <div class="profile-links">
        <?php if ($is_own_profile): ?>
            <a href="edit_profile.php">Edit Profile</a>
            <a href="upload_video.php">Upload Video</a>
            <a href="view_videos.php">View Videos</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="send_message.php?to=<?php echo $view_user_id; ?>">Send Message</a>
            <a href="home.php">Back to Home</a>
        <?php endif; ?>
    </div>

    <!-- Videos Section -->
    <div class="videos">
        <h3>Videos by <?php echo htmlspecialchars($username); ?></h3>
        <div class="video-grid">
            <?php
            if ($video_stmt->num_rows > 0) {
                while ($video_stmt->fetch()) {
                    echo '<div class="video-item">';
                    echo '<p>' . htmlspecialchars($video_title) . '</p>';

                    if ($price_in_credits == 0) {
                        echo '<video controls>';
                    } else if (in_array($video_id, $purchased_videos)) {
                        echo '<video controls>';
                    } else {
                        echo '<video style="filter: blur(5px); pointer-events: none;">';
                    }

                    echo '<source src="uploads/videos/' . htmlspecialchars($video_filename) . '" type="video/mp4">';
                    echo '</video>';

                    if ($price_in_credits > 0 && !in_array($video_id, $purchased_videos)) {
                        echo '<div class="locked-overlay">🔒<div class="price-tag">' . $price_in_credits . ' Credits</div></div>';
                        echo '<a href="purchase_video.php?video_id=' . $video_id . '" class="purchase-btn">Buy</a>';
                    } elseif (in_array($video_id, $purchased_videos)) {
                        echo '<div class="purchased-overlay">✔ Purchased</div>';
                    }

                    echo '<a href="video_comments.php?id=' . $video_id . '" class="view-comments-btn">View Comments</a>';
                    echo '</div>';
                }
            } else {
                echo "<p style='text-align:center;'>No videos uploaded yet.</p>";
            }
            ?>
        </div>
    </div>
</div>

<div style="text-align:center; margin: 30px 0;">
    <a href="home.php" class="home-button">🏠 Back to Home</a>
</div>


</body>
</html>

