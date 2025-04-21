<?php
session_start();
include "db.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's uploaded videos
$query = "SELECT id, title, description, filename FROM videos WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($video_id, $title, $description, $filename);

// Start the HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Videos</title>
</head>
<body>
    <h2>Your Uploaded Videos</h2>

    <?php while ($stmt->fetch()): ?>
	    <div>
		<h3><?php echo htmlspecialchars($title); ?></h3>
		<p><?php echo htmlspecialchars($description ? $description : "No description provided."); ?></p>
		<video width="300" controls>
		    <source src="uploads/videos/<?php echo htmlspecialchars($filename); ?>" type="video/<?php echo pathinfo($filename, PATHINFO_EXTENSION); ?>">
		    Your browser does not support the video tag.
		</video>
		<br>
		<!-- Options to Edit or Delete -->
		<a href="edit_video.php?id=<?php echo $video_id; ?>">Edit</a> | 
		<a href="delete_video.php?id=<?php echo $video_id; ?>">Delete</a> |

		<!-- Link to the comments page -->
		<a href="video_comments.php?id=<?php echo $video_id; ?>">View Comments</a>
	    </div>
	    <br>
	<?php endwhile; ?>


    <a href="profile.php">Back to Profile</a>

</body>
</html>
<?php
$stmt->close();
?>
